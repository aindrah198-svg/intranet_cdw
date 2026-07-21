<?php
// app/Views/direktur/approval/cuti_detail.php

$cuti = $cuti ?? [];
$cutiId = $cuti['id'] ?? 0;
$isPending = ($cuti['status_direktur'] ?? '') === 'Menunggu' && ($cuti['status_hrd'] ?? '') === 'Disetujui';

// Helper function untuk format tanggal
if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) return '-';
        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : '-';
    }
}

// Helper function untuk format datetime
if (!function_exists('formatDateTime')) {
    function formatDateTime($datetime) {
        if (empty($datetime)) return '-';
        $timestamp = strtotime($datetime);
        return $timestamp ? date('d/m/Y H:i', $timestamp) : '-';
    }
}

// Helper function untuk badge status
if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status) {
        $badges = [
            'Menunggu' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>',
            'Disetujui' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>',
            'Tidak Diperlukan' => '<span class="badge bg-secondary"><i class="fas fa-minus me-1"></i>Tidak Diperlukan</span>'
        ];
        return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
}

// Helper function untuk badge jenis cuti
if (!function_exists('getJenisCutiBadge')) {
    function getJenisCutiBadge($jenis) {
        $badges = [
            'Tahunan' => '<span class="badge bg-success">Cuti Tahunan</span>',
            'Sakit' => '<span class="badge bg-danger">Cuti Sakit</span>',
            'Hamil' => '<span class="badge bg-warning">Cuti Hamil</span>',
            'Penting' => '<span class="badge bg-info">Cuti Penting</span>',
            'Izin' => '<span class="badge bg-secondary">Izin</span>',
            'Lainnya' => '<span class="badge bg-dark">Lainnya</span>'
        ];
        return $badges[$jenis] ?? '<span class="badge bg-secondary">' . $jenis . '</span>';
    }
}
?>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient">
                <i class="fas fa-calendar-alt me-2"></i>Detail Pengajuan Cuti
            </h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/approval/cuti') ?>" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </p>
        </div>
        <div>
            <a href="<?= base_url('direktur/approval/cuti/print/' . $cutiId) ?>" 
               class="btn btn-modern-outline me-2" target="_blank">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
            <?php if ($isPending): ?>
            <button type="button" class="btn btn-success approve-btn" data-id="<?= $cutiId ?>">
                <i class="fas fa-check me-2"></i>Setujui
            </button>
            <button type="button" class="btn btn-danger reject-btn" data-id="<?= $cutiId ?>" data-nama="<?= htmlspecialchars($cuti['nama_panggilan'] ?? $cuti['nama_lengkap'] ?? '') ?>">
                <i class="fas fa-times me-2"></i>Tolak
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Informasi Pengajuan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Informasi Pengajuan
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Nomor Pengajuan</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($cuti['nomor_cuti'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Pengajuan</label>
                        <p class="fw-bold mb-0"><?= formatDateTime($cuti['tanggal_pengajuan'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Jenis Cuti</label>
                        <p class="mb-0"><?= getJenisCutiBadge($cuti['jenis_cuti'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Status Pengajuan</label>
                        <p class="mb-0">
                            <?php 
                            $statusPengajuan = $cuti['status_pengajuan'] ?? 'Draft';
                            $badgeClass = [
                                'Draft' => 'secondary',
                                'Menunggu Atasan' => 'info',
                                'Menunggu HRD' => 'primary',
                                'Disetujui' => 'success',
                                'Ditolak' => 'danger',
                                'Dibatalkan' => 'dark'
                            ];
                            ?>
                            <span class="badge bg-<?= $badgeClass[$statusPengajuan] ?? 'secondary' ?>">
                                <?= $statusPengajuan ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Alasan Cuti</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($cuti['alasan'] ?? '-')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Cuti -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-calendar-week me-2 text-primary"></i>
                    Detail Cuti
                </h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Mulai</label>
                        <p class="fw-bold mb-0"><?= formatDate($cuti['tanggal_mulai'] ?? '') ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Selesai</label>
                        <p class="fw-bold mb-0"><?= formatDate($cuti['tanggal_selesai'] ?? '') ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small mb-1 d-block">Lama Cuti</label>
                        <p class="fw-bold mb-0">
                            <span class="badge bg-primary"><?= $cuti['lama_hari'] ?? 0 ?> Hari</span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Sisa Cuti Tahunan</label>
                        <p class="mb-0"><?= $cuti['sisa_cuti_tahunan'] ?? 12 ?> Hari</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Pejabat Penerima Tugas</label>
                        <p class="mb-0"><?= htmlspecialchars($cuti['pejabat_penerima_tugas'] ?? '-') ?></p>
                    </div>
                    <?php if (!empty($cuti['alamat_selama_cuti'])): ?>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Alamat Selama Cuti</label>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($cuti['alamat_selama_cuti'])) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($cuti['no_telepon_cuti'])): ?>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">No Telepon Selama Cuti</label>
                        <p class="mb-0"><?= htmlspecialchars($cuti['no_telepon_cuti']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($cuti['dokumen_pendukung'])): ?>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Dokumen Pendukung</label>
                        <a href="<?= base_url($cuti['dokumen_pendukung']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-file-download me-1"></i> Lihat Dokumen
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Riwayat Approval -->
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-history me-2 text-primary"></i>
                    Riwayat Persetujuan
                </h5>
                <div class="timeline">
                    <!-- Approval Atasan -->
                    <div class="d-flex mb-4">
                        <div class="timeline-icon me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center 
                                <?= ($cuti['status_atasan'] ?? '') === 'Disetujui' ? 'bg-success' : (($cuti['status_atasan'] ?? '') === 'Ditolak' ? 'bg-danger' : 'bg-secondary') ?>" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-user-tie text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1">Persetujuan Atasan</h6>
                                <?php if (($cuti['status_atasan'] ?? '') === 'Disetujui'): ?>
                                    <span class="badge bg-success">Disetujui</span>
                                <?php elseif (($cuti['status_atasan'] ?? '') === 'Ditolak'): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($cuti['atasan_nama'])): ?>
                            <p class="mb-1 text-muted small">Oleh: <?= htmlspecialchars($cuti['atasan_nama']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($cuti['tanggal_disetujui_atasan'])): ?>
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-clock me-1"></i> <?= formatDateTime($cuti['tanggal_disetujui_atasan']) ?>
                            </p>
                            <?php endif; ?>
                            <?php if (!empty($cuti['alasan_penolakan_atasan'])): ?>
                            <div class="alert alert-danger mt-2 mb-0 p-2 small">
                                <i class="fas fa-info-circle me-1"></i> 
                                Alasan: <?= htmlspecialchars($cuti['alasan_penolakan_atasan']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Approval HRD -->
                    <div class="d-flex mb-4">
                        <div class="timeline-icon me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center 
                                <?= ($cuti['status_hrd'] ?? '') === 'Disetujui' ? 'bg-success' : (($cuti['status_hrd'] ?? '') === 'Ditolak' ? 'bg-danger' : 'bg-secondary') ?>" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1">Persetujuan HRD</h6>
                                <?php if (($cuti['status_hrd'] ?? '') === 'Disetujui'): ?>
                                    <span class="badge bg-success">Disetujui</span>
                                <?php elseif (($cuti['status_hrd'] ?? '') === 'Ditolak'): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($cuti['hrd_nama'])): ?>
                            <p class="mb-1 text-muted small">Oleh: <?= htmlspecialchars($cuti['hrd_nama']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($cuti['tanggal_disetujui_hrd'])): ?>
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-clock me-1"></i> <?= formatDateTime($cuti['tanggal_disetujui_hrd']) ?>
                            </p>
                            <?php endif; ?>
                            <?php if (!empty($cuti['catatan_hrd'])): ?>
                            <div class="alert alert-info mt-2 mb-0 p-2 small">
                                <i class="fas fa-sticky-note me-1"></i> 
                                Catatan: <?= htmlspecialchars($cuti['catatan_hrd']) ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($cuti['alasan_penolakan_hrd'])): ?>
                            <div class="alert alert-danger mt-2 mb-0 p-2 small">
                                <i class="fas fa-info-circle me-1"></i> 
                                Alasan: <?= htmlspecialchars($cuti['alasan_penolakan_hrd']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Approval Direktur -->
                    <div class="d-flex">
                        <div class="timeline-icon me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center 
                                <?= ($cuti['status_direktur'] ?? '') === 'Disetujui' ? 'bg-success' : (($cuti['status_direktur'] ?? '') === 'Ditolak' ? 'bg-danger' : 'bg-secondary') ?>" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-crown text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1">Persetujuan Direktur</h6>
                                <?= getStatusBadge($cuti['status_direktur'] ?? 'Menunggu') ?>
                            </div>
                            <?php if (!empty($cuti['direktur_nama'])): ?>
                            <p class="mb-1 text-muted small">Oleh: <?= htmlspecialchars($cuti['direktur_nama']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($cuti['tanggal_disetujui_direktur'])): ?>
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-clock me-1"></i> <?= formatDateTime($cuti['tanggal_disetujui_direktur']) ?>
                            </p>
                            <?php endif; ?>
                            <?php if (!empty($cuti['alasan_penolakan_direktur'])): ?>
                            <div class="alert alert-danger mt-2 mb-0 p-2 small">
                                <i class="fas fa-info-circle me-1"></i> 
                                Alasan: <?= htmlspecialchars($cuti['alasan_penolakan_direktur']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Informasi Karyawan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-user-circle me-2 text-primary"></i>
                    Informasi Karyawan
                </h5>
                <div class="text-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" 
                         style="width: 80px; height: 80px; font-size: 32px;">
                        <?= strtoupper(substr($cuti['nama_panggilan'] ?? $cuti['nama_lengkap'] ?? '?', 0, 1)) ?>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($cuti['nama_panggilan'] ?? $cuti['nama_lengkap'] ?? '-') ?></h5>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($cuti['nik'] ?? '-') ?></p>
                </div>
                <hr>
                <div class="mb-2">
                    <i class="fas fa-briefcase text-primary me-2" style="width: 20px;"></i>
                    <strong>Jabatan:</strong> <?= htmlspecialchars($cuti['jabatan'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-building text-primary me-2" style="width: 20px;"></i>
                    <strong>Departemen:</strong> <?= htmlspecialchars($cuti['departemen'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-calendar-alt text-primary me-2" style="width: 20px;"></i>
                    <strong>Tanggal Masuk:</strong> <?= formatDate($cuti['karyawan_tanggal_masuk'] ?? '') ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <?php if ($isPending): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-bolt me-2 text-primary"></i>
                    Tindakan Cepat
                </h5>
                <div class="d-grid gap-2">
                    <button class="btn btn-success approve-btn" data-id="<?= $cutiId ?>">
                        <i class="fas fa-check me-2"></i>Setujui Pengajuan
                    </button>
                    <button class="btn btn-danger reject-btn" data-id="<?= $cutiId ?>" data-nama="<?= htmlspecialchars($cuti['nama_panggilan'] ?? $cuti['nama_lengkap'] ?? '') ?>">
                        <i class="fas fa-times me-2"></i>Tolak Pengajuan
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle text-danger me-2"></i>
                    Tolak Pengajuan Cuti
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejectId">
                    <div class="mb-3">
                        <label class="form-label">Karyawan</label>
                        <p class="fw-bold" id="rejectNama"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasanPenolakan" class="form-control" rows="4" 
                                  placeholder="Isikan alasan penolakan pengajuan cuti..."></textarea>
                        <small class="text-muted">Alasan akan dicatat sebagai riwayat</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Single Approve
    $('.approve-btn').on('click', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Setujui pengajuan cuti ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/cuti/approve') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '<?= base_url('direktur/approval/cuti') ?>';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan, silakan coba lagi'
                        });
                    }
                });
            }
        });
    });
    
    // Reject modal
    $('.reject-btn').on('click', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        
        $('#rejectId').val(id);
        $('#rejectNama').text(nama);
        $('#alasanPenolakan').val('');
        $('#rejectModal').modal('show');
    });
    
    // Submit Reject
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        
        var id = $('#rejectId').val();
        var alasan = $('#alasanPenolakan').val();
        
        if (!alasan.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Alasan penolakan harus diisi'
            });
            return;
        }
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Tolak pengajuan cuti ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/cuti/reject') ?>/' + id,
                    type: 'POST',
                    data: { alasan: alasan },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#rejectModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '<?= base_url('direktur/approval/cuti') ?>';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan, silakan coba lagi'
                        });
                    }
                });
            }
        });
    });
});
</script>

<style>
.modern-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.text-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.btn-modern-outline {
    border: 1px solid #4e73df;
    background: transparent;
    color: #4e73df;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.2s;
}
.btn-modern-outline:hover {
    background: #4e73df;
    color: white;
}
.timeline-icon {
    flex-shrink: 0;
}
</style>