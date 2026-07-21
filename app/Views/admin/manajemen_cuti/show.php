<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\manajemen_cuti\show.php
$title = 'Detail Pengajuan Cuti';
$active = 'cuti';
$css = [
    'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'
];
$scripts = [
    'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js'
];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pengajuan Cuti</h1>
        <div>
            <a href="<?= base_url('admin/cuti'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
            <a href="<?= base_url('admin/cuti/export/pdf/' . $cuti['id']); ?>" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm">
                <i class="fas fa-file-pdf fa-sm text-white-50"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Detail Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Cuti</h6>
                    <span class="badge bg-<?= getStatusBadgeClass($cuti['status'] ?? 'Draft') ?>">
                        <?= $cuti['status'] ?? 'Draft'; ?>
                    </span>
                </div>
                <div class="card-body">
                    <!-- Nomor Cuti & Tanggal -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <small class="text-muted">Nomor Cuti</small>
                                <h5><?= esc($cuti['nomor_cuti'] ?? '-'); ?></h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <small class="text-muted">Tanggal Pengajuan</small>
                                <h5><?= !empty($cuti['tanggal_pengajuan']) ? date('d F Y H:i', strtotime($cuti['tanggal_pengajuan'])) : '-'; ?></h5>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Karyawan -->
                    <h6 class="font-weight-bold text-primary mb-3">Informasi Karyawan</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <small class="text-muted">NIK</small>
                                <p class="font-weight-bold"><?= esc($cuti['nik'] ?? '-'); ?></p>
                            </div>
                            <div class="info-item mb-3">
                                <small class="text-muted">Nama Lengkap</small>
                                <p class="font-weight-bold"><?= esc($cuti['nama_lengkap'] ?? '-'); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <small class="text-muted">Jabatan</small>
                                <p class="font-weight-bold"><?= esc($cuti['jabatan'] ?? '-'); ?></p>
                            </div>
                            <div class="info-item mb-3">
                                <small class="text-muted">Departemen</small>
                                <p class="font-weight-bold"><?= esc($cuti['departemen'] ?? '-'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Cuti -->
                    <h6 class="font-weight-bold text-primary mb-3">Detail Cuti</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <small class="text-muted">Jenis Cuti</small>
                                <p class="font-weight-bold">
                                    <?= esc($cuti['jenis_cuti'] ?? '-'); ?>
                                    <?php if (($cuti['jenis_cuti'] ?? '') === 'Tahunan' && !empty($cuti['sisa_cuti_tahunan'])): ?>
                                        <br><small class="text-muted">Sisa cuti tahunan: <?= $cuti['sisa_cuti_tahunan']; ?> hari</small>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="info-item mb-3">
                                <small class="text-muted">Periode Cuti</small>
                                <p class="font-weight-bold">
                                    <?= !empty($cuti['tanggal_mulai']) ? date('d F Y', strtotime($cuti['tanggal_mulai'])) : '-'; ?><br>
                                    <?php if (!empty($cuti['tanggal_mulai']) && !empty($cuti['tanggal_selesai'])): ?>
                                        <small class="text-muted">s/d</small><br>
                                        <?= date('d F Y', strtotime($cuti['tanggal_selesai'])); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <small class="text-muted">Lama Cuti</small>
                                <p class="font-weight-bold">
                                    <?= $cuti['lama_hari'] ?? 0; ?> hari kerja
                                    <br>
                                    <small class="text-muted">
                                        <?php 
                                        if (!empty($cuti['tanggal_mulai']) && !empty($cuti['tanggal_selesai'])) {
                                            $totalDays = (strtotime($cuti['tanggal_selesai']) - strtotime($cuti['tanggal_mulai'])) / (60 * 60 * 24) + 1;
                                            $lamaHari = $cuti['lama_hari'] ?? 0;
                                            $weekendDays = $totalDays - $lamaHari;
                                            echo "($totalDays hari kalender, $weekendDays hari weekend)";
                                        } else {
                                            echo "(0 hari kalender)";
                                        }
                                        ?>
                                    </small>
                                </p>
                            </div>
                            <div class="info-item mb-3">
                                <small class="text-muted">Status</small>
                                <p class="font-weight-bold">
                                    <span class="badge bg-<?= getStatusBadgeClass($cuti['status'] ?? 'Draft') ?>">
                                        <?= $cuti['status'] ?? 'Draft'; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

<!-- Alasan Cuti -->
<h6 class="font-weight-bold text-primary mb-3">Alasan Cuti</h6>
<div class="card bg-light mb-4">
    <div class="card-body">
        <p class="mb-0">
            <?php 
            $alasan = $cuti['alasan'] ?? '';
            if (is_string($alasan) && !empty($alasan)) {
                echo nl2br(esc($alasan));
            } else {
                echo '<span class="text-muted">-</span>';
            }
            ?>
        </p>
    </div>
</div>

                    <!-- Status Persetujuan -->
                    <h6 class="font-weight-bold text-primary mb-3">Status Persetujuan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <small class="text-muted">Disetujui Oleh</small>
                                <p class="font-weight-bold"><?= esc($cuti['disetujui_nama'] ?? '-'); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <small class="text-muted">Disetujui Pada</small>
                                <p class="font-weight-bold">
                                    <?= !empty($cuti['disetujui_at']) ? 
                                        date('d F Y H:i', strtotime($cuti['disetujui_at'])) : '-'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

<!-- Alasan Penolakan -->
<?php if (!empty($cuti['alasan_penolakan'])): ?>
<h6 class="font-weight-bold text-danger mb-3">Alasan Penolakan</h6>
<div class="alert alert-danger">
    <i class="fas fa-times-circle me-2"></i>
    <?php 
    $alasanPenolakan = $cuti['alasan_penolakan'] ?? '';
    if (is_string($alasanPenolakan) && !empty($alasanPenolakan)) {
        echo nl2br(esc($alasanPenolakan));
    } else {
        echo '-';
    }
    ?>
</div>
<?php endif; ?>

            <!-- Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Timeline Pengajuan</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Step 1: Pengajuan -->
                        <div class="timeline-step <?= ($cuti['status'] ?? 'Draft') !== 'Draft' ? 'timeline-step-success' : '' ?>">
                            <div class="timeline-content">
                                <div class="inner-circle <?= ($cuti['status'] ?? 'Draft') !== 'Draft' ? 'bg-success' : 'bg-secondary' ?>">
                                    <i class="fas fa-paper-plane text-white"></i>
                                </div>
                                <p class="h6 mt-2 mb-0">Pengajuan</p>
                                <p class="text-muted mb-0">
                                    <?= !empty($cuti['tanggal_pengajuan']) ? date('d/m/Y', strtotime($cuti['tanggal_pengajuan'])) : '-'; ?>
                                </p>
                                <?php if (($cuti['status'] ?? 'Draft') === 'Draft'): ?>
                                    <span class="badge bg-secondary mt-1">Draft</span>
                                <?php else: ?>
                                    <span class="badge bg-success mt-1">Selesai</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Step 2: Review HRD -->
                        <div class="timeline-step <?= in_array($cuti['status'] ?? '', ['Disetujui HRD', 'Disetujui Atasan', 'Ditolak']) ? 'timeline-step-success' : '' ?>">
                            <div class="timeline-content">
                                <div class="inner-circle <?= in_array($cuti['status'] ?? '', ['Disetujui HRD', 'Disetujui Atasan', 'Ditolak']) ? 'bg-success' : 'bg-secondary' ?>">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                                <p class="h6 mt-2 mb-0">Review HRD</p>
                                <?php if (in_array($cuti['status'] ?? '', ['Disetujui HRD', 'Disetujui Atasan', 'Ditolak'])): ?>
                                    <p class="text-muted mb-0">
                                        <?= !empty($cuti['disetujui_at']) ? date('d/m/Y', strtotime($cuti['disetujui_at'])) : '-' ?>
                                    </p>
                                    <?php if (($cuti['status'] ?? '') === 'Ditolak'): ?>
                                        <span class="badge bg-danger mt-1">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-success mt-1">Selesai</span>
                                    <?php endif; ?>
                                <?php elseif (($cuti['status'] ?? '') === 'Menunggu'): ?>
                                    <span class="badge bg-warning mt-1">Menunggu</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary mt-1">Belum</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Step 3: Persetujuan Atasan -->
                        <div class="timeline-step <?= ($cuti['status'] ?? '') === 'Disetujui Atasan' ? 'timeline-step-success' : '' ?>">
                            <div class="timeline-content">
                                <div class="inner-circle <?= ($cuti['status'] ?? '') === 'Disetujui Atasan' ? 'bg-success' : 'bg-secondary' ?>">
                                    <i class="fas fa-user-shield text-white"></i>
                                </div>
                                <p class="h6 mt-2 mb-0">Atasan</p>
                                <?php if (($cuti['status'] ?? '') === 'Disetujui Atasan'): ?>
                                    <p class="text-muted mb-0">
                                        <?= !empty($cuti['disetujui_at']) ? date('d/m/Y', strtotime($cuti['disetujui_at'])) : '-' ?>
                                    </p>
                                    <span class="badge bg-success mt-1">Disetujui</span>
                                <?php elseif (($cuti['status'] ?? '') === 'Disetujui HRD'): ?>
                                    <span class="badge bg-warning mt-1">Menunggu</span>
                                <?php elseif (($cuti['status'] ?? '') === 'Ditolak'): ?>
                                    <span class="badge bg-danger mt-1">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary mt-1">Belum</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-lg-4">
            <!-- Action Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if (in_array($cuti['status'] ?? '', ['Draft', 'Menunggu'])): ?>
                            <a href="<?= base_url('admin/cuti/edit/' . $cuti['id']); ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-edit me-2"></i> Edit
                            </a>
                            
                            <?php if (session()->get('role') === 'hrd' && ($cuti['status'] ?? '') === 'Menunggu'): ?>
                                <button type="button" class="btn btn-success btn-block" onclick="approveCuti()">
                                    <i class="fas fa-check me-2"></i> Setujui
                                </button>
                                
                                <button type="button" class="btn btn-danger btn-block" onclick="rejectCuti()">
                                    <i class="fas fa-times me-2"></i> Tolak
                                </button>
                            <?php endif; ?>
                            
                            <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete()">
                                <i class="fas fa-trash me-2"></i> Hapus
                            </button>
                            
                            <?php if (($cuti['status'] ?? '') === 'Menunggu'): ?>
                                <button type="button" class="btn btn-secondary btn-block" onclick="cancelCuti()">
                                    <i class="fas fa-times-circle me-2"></i> Batalkan
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Cetak -->
                        <a href="<?= base_url('admin/cuti/export/pdf/' . $cuti['id']); ?>" class="btn btn-danger btn-block" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i> Cetak PDF
                        </a>
                        
                        <!-- History -->
                        <a href="<?= base_url('admin/cuti'); ?>" class="btn btn-info btn-block">
                            <i class="fas fa-history me-2"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informasi Kuota -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Informasi Kuota</h6>
                </div>
                <div class="card-body">
                    <?php if (($cuti['jenis_cuti'] ?? '') === 'Tahunan' && !empty($cuti['sisa_cuti_tahunan'])): ?>
                        <div class="text-center mb-3">
                            <div class="circular-progress mx-auto" style="width: 120px; height: 120px;">
                                <svg width="120" height="120" viewBox="0 0 120 120">
                                    <!-- Background circle -->
                                    <circle cx="60" cy="60" r="54" fill="none" stroke="#e9ecef" stroke-width="12"/>
                                    <!-- Progress circle -->
                                    <?php 
                                    $sisa = $cuti['sisa_cuti_tahunan'];
                                    $terpakai = 12 - $sisa;
                                    $percentage = ($terpakai / 12) * 100;
                                    $circumference = 2 * 3.1416 * 54;
                                    $progress = $circumference * $percentage / 100;
                                    ?>
                                    <circle cx="60" cy="60" r="54" fill="none" stroke="#28a745" stroke-width="12"
                                            stroke-dasharray="<?= $progress ?> <?= $circumference ?>"
                                            stroke-linecap="round"
                                            transform="rotate(-90 60 60)"/>
                                </svg>
                                <div style="position: absolute; width: 96px; height: 96px; 
                                            border-radius: 50%; top: 12px; left: 12px; 
                                            display: flex; flex-direction: column; 
                                            justify-content: center; align-items: center;
                                            transform: translate(12px, 12px);">
                                    <span style="font-size: 1.5rem; font-weight: bold; color: #28a745;">
                                        <?= $sisa; ?>
                                    </span>
                                    <span style="font-size: 0.8rem; color: #6c757d;">Sisa</span>
                                </div>
                            </div>
                            <h5 class="mt-2">Sisa Cuti Tahunan</h5>
                            <h2 class="text-success"><?= $cuti['sisa_cuti_tahunan']; ?> hari</h2>
                            <p class="text-muted">Pada saat pengajuan</p>
                        </div>
                        
                        <?php 
                        $terpakai = 12 - ($cuti['sisa_cuti_tahunan'] ?? 0);
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
                            <h5 class="mt-2">Jenis Cuti: <?= $cuti['jenis_cuti'] ?? '-'; ?></h5>
                            <p class="text-muted">
                                <?php if (($cuti['jenis_cuti'] ?? '') === 'Hamil'): ?>
                                    Cuti hamil tidak mengurangi kuota tahunan
                                <?php elseif (($cuti['jenis_cuti'] ?? '') === 'Sakit'): ?>
                                    Cuti sakit dengan surat dokter
                                <?php elseif (($cuti['jenis_cuti'] ?? '') === 'Khusus'): ?>
                                    Cuti khusus (pernikahan, kelahiran, dll)
                                <?php else: ?>
                                    Cuti lainnya (izin pribadi)
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informasi Sistem -->
            <div class="card shadow">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Informasi Sistem</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span>ID Cuti:</span>
                            <span class="text-muted"><?= $cuti['id'] ?? '-'; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Created At:</span>
                            <span class="text-muted"><?= !empty($cuti['created_at']) ? date('d/m/Y H:i', strtotime($cuti['created_at'])) : '-'; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Updated At:</span>
                            <span class="text-muted"><?= !empty($cuti['updated_at']) ? date('d/m/Y H:i', strtotime($cuti['updated_at'])) : '-'; ?></span>
                        </div>
                        <hr>
                        <div class="text-center">
                            <i class="fas fa-info-circle text-info"></i>
                            <small class="text-muted">Dokumen ID: <?= $cuti['nomor_cuti'] ?? '-'; ?></small>
                        </div>
                    </div>
                </div>
            </div>
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
                <form action="<?= base_url('admin/cuti/delete/' . ($cuti['id'] ?? '')); ?>" method="post" style="display: inline;">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Persetujuan Cuti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/cuti/approve/' . ($cuti['id'] ?? '')); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui pengajuan cuti ini?</p>
                    <div class="form-group">
                        <label for="approve_notes">Catatan (opsional):</label>
                        <textarea class="form-control" id="approve_notes" name="notes" rows="3" 
                                  placeholder="Tambahkan catatan persetujuan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Penolakan Cuti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/cuti/reject/' . ($cuti['id'] ?? '')); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak pengajuan cuti ini?</p>
                    <div class="form-group">
                        <label for="reject_reason" class="text-danger">Alasan Penolakan *</label>
                        <textarea class="form-control" id="reject_reason" name="alasan_penolakan" 
                                  rows="4" placeholder="Jelaskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pembatalan Cuti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/cuti/cancel/' . ($cuti['id'] ?? '')); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan pengajuan cuti ini?</p>
                    <div class="form-group">
                        <label for="cancel_reason">Alasan Pembatalan:</label>
                        <textarea class="form-control" id="cancel_reason" name="cancel_reason" 
                                  rows="3" placeholder="Jelaskan alasan pembatalan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Batalkan</button>
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
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}

.info-item {
    margin-bottom: 15px;
}

.info-item small {
    font-size: 0.8rem;
}

.circular-progress {
    position: relative;
    width: 120px;
    height: 120px;
}
</style>

<script>
function confirmDelete() {
    $('#deleteModal').modal('show');
}

function approveCuti() {
    $('#approveModal').modal('show');
}

function rejectCuti() {
    $('#rejectModal').modal('show');
}

function cancelCuti() {
    $('#cancelModal').modal('show');
}

// SweetAlert for success messages
<?php if (session()->has('success')): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= session('success') ?>',
    timer: 3000,
    showConfirmButton: false
});
<?php endif; ?>

<?php if (session()->has('error')): ?>
Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: '<?= session('error') ?>',
    timer: 3000,
    showConfirmButton: false
});
<?php endif; ?>

// Auto-hide success message after 5 seconds
setTimeout(function() {
    $('.alert').alert('close');
}, 5000);
</script>

<?= $this->include('admin/templates/footer') ?>