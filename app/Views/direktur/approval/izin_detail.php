<?php
// app/Views/direktur/approval/izin_detail.php

$izin = $izin ?? [];
$izinId = $izin['id'] ?? 0;
$jamIzin = $jamIzin ?? '';
$isPending = ($izin['status_keseluruhan'] ?? '') === 'Menunggu' && ($izin['status_hrd'] ?? '') === 'Disetujui';

// Helper function untuk format tanggal
if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) return '-';
        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : '-';
    }
}

// Helper function untuk format tanggal Indonesia
if (!function_exists('formatDateIndo')) {
    function formatDateIndo($date) {
        if (empty($date)) return '-';
        $timestamp = strtotime($date);
        if (!$timestamp) return '-';
        
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $tgl = date('d', $timestamp);
        $bln = $bulan[(int)date('m', $timestamp)];
        $thn = date('Y', $timestamp);
        
        return $tgl . ' ' . $bln . ' ' . $thn;
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

// Helper function untuk format waktu (H:i)
if (!function_exists('formatTime')) {
    function formatTime($time) {
        if (empty($time)) return '-';
        $timestamp = strtotime($time);
        return $timestamp ? date('H:i', $timestamp) : '-';
    }
}

// Helper function untuk badge status
if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status) {
        $badges = [
            'Menunggu' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>',
            'Disetujui' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>',
            'Dibatalkan' => '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>Dibatalkan</span>'
        ];
        return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
}

// Helper function untuk badge jenis izin
if (!function_exists('getJenisIzinBadge')) {
    function getJenisIzinBadge($jenis) {
        $badges = [
            'Izin' => '<span class="badge bg-info">Izin</span>',
            'Sakit Ringan' => '<span class="badge bg-danger">Sakit Ringan</span>',
            'Keperluan Keluarga' => '<span class="badge bg-warning text-dark">Keperluan Keluarga</span>',
            'Keperluan Mendadak' => '<span class="badge bg-primary">Keperluan Mendadak</span>',
            'Lainnya' => '<span class="badge bg-secondary">Lainnya</span>'
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
                <i class="fas fa-user-edit me-2"></i>Detail Pengajuan Izin
            </h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/approval/izin') ?>" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </p>
        </div>
        <div>
            <a href="<?= base_url('direktur/approval/izin/print/' . $izinId) ?>" 
               class="btn btn-modern-outline me-2" target="_blank">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
            <?php if ($isPending): ?>
            <button type="button" class="btn btn-success approve-btn" data-id="<?= $izinId ?>" data-nomor="<?= htmlspecialchars($izin['nomor_izin'] ?? '') ?>" data-nama="<?= htmlspecialchars($izin['nama_panggilan'] ?? $izin['nama_lengkap'] ?? '') ?>">
                <i class="fas fa-check me-2"></i>Setujui Izin
            </button>
            <button type="button" class="btn btn-danger reject-btn" data-id="<?= $izinId ?>" data-nomor="<?= htmlspecialchars($izin['nomor_izin'] ?? '') ?>" data-nama="<?= htmlspecialchars($izin['nama_panggilan'] ?? $izin['nama_lengkap'] ?? '') ?>">
                <i class="fas fa-times me-2"></i>Tolak Izin
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
                        <label class="text-muted small mb-1 d-block">Nomor Izin</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($izin['nomor_izin'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Pengajuan</label>
                        <p class="mb-0"><?= formatDateTime($izin['tanggal_pengajuan'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Jenis Izin</label>
                        <p class="mb-0"><?= getJenisIzinBadge($izin['jenis_izin'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Status Izin</label>
                        <p class="mb-0"><?= getStatusBadge($izin['status_keseluruhan'] ?? 'Menunggu') ?></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Alasan Izin</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($izin['alasan'] ?? '-')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Izin -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                    Detail Izin
                </h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Mulai</label>
                        <p class="fw-bold mb-0"><?= formatDateIndo($izin['tanggal_mulai'] ?? '') ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Selesai</label>
                        <p class="fw-bold mb-0"><?= formatDateIndo($izin['tanggal_selesai'] ?? '') ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small mb-1 d-block">Lama Izin</label>
                        <p class="fw-bold mb-0">
                            <span class="badge bg-primary"><?= $izin['lama_hari'] ?? 0 ?> Hari</span>
                        </p>
                    </div>
                    
                    <?php if (!empty($izin['jam_keluar']) || !empty($izin['jam_kembali'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Jam Keluar</label>
                        <p class="mb-0"><?= formatTime($izin['jam_keluar'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Jam Kembali</label>
                        <p class="mb-0"><?= formatTime($izin['jam_kembali'] ?? '') ?></p>
                    </div>
                    <?php if (!empty($jamIzin)): ?>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Durasi Izin</label>
                        <p class="mb-0 text-info fw-bold"><?= $jamIzin ?></p>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!empty($izin['dokumen_pendukung'])): ?>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Dokumen Pendukung</label>
                        <a href="<?= base_url($izin['dokumen_pendukung']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-file-download me-1"></i> Lihat Dokumen
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Persetujuan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Status Persetujuan
                </h5>
                <div class="row">
                    <div class="col-md-4 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-user-tie fa-2x mb-2 <?= ($izin['status_atasan'] ?? '') === 'Disetujui' ? 'text-success' : (($izin['status_atasan'] ?? '') === 'Ditolak' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Persetujuan Atasan</h6>
                            <?php if (($izin['status_atasan'] ?? '') === 'Disetujui'): ?>
                                <span class="badge bg-success">Disetujui</span>
                                <?php if (!empty($izin['atasan_nama'])): ?>
                                    <small class="d-block mt-1">Oleh: <?= htmlspecialchars($izin['atasan_nama']) ?></small>
                                <?php endif; ?>
                            <?php elseif (($izin['status_atasan'] ?? '') === 'Ditolak'): ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-users fa-2x mb-2 <?= ($izin['status_hrd'] ?? '') === 'Disetujui' ? 'text-success' : (($izin['status_hrd'] ?? '') === 'Ditolak' ? 'text-danger' : 'text-secondary') ?>"></i>
                            <h6 class="mb-1">Persetujuan HRD</h6>
                            <?php if (($izin['status_hrd'] ?? '') === 'Disetujui'): ?>
                                <span class="badge bg-success">Disetujui</span>
                                <?php if (!empty($izin['hrd_nama'])): ?>
                                    <small class="d-block mt-1">Oleh: <?= htmlspecialchars($izin['hrd_nama']) ?></small>
                                <?php endif; ?>
                            <?php elseif (($izin['status_hrd'] ?? '') === 'Ditolak'): ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Tidak Diperlukan</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-crown fa-2x mb-2 <?= ($izin['status_keseluruhan'] ?? '') === 'Disetujui' ? 'text-success' : (($izin['status_keseluruhan'] ?? '') === 'Ditolak' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Status Akhir</h6>
                            <?= getStatusBadge($izin['status_keseluruhan'] ?? 'Menunggu') ?>
                            <?php if (!empty($izin['alasan_penolakan_atasan']) && ($izin['status_keseluruhan'] ?? '') === 'Ditolak'): ?>
                                <small class="d-block mt-1 text-danger">
                                    <i class="fas fa-info-circle me-1"></i> <?= htmlspecialchars(substr($izin['alasan_penolakan_atasan'], 0, 50)) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if (!empty($izin['alasan_penolakan_atasan']) && ($izin['status_keseluruhan'] ?? '') === 'Ditolak'): ?>
                <div class="mt-3 p-3 bg-danger bg-opacity-10 rounded">
                    <strong class="text-danger"><i class="fas fa-ban me-1"></i> Alasan Penolakan:</strong>
                    <p class="mt-1 mb-0"><?= nl2br(htmlspecialchars($izin['alasan_penolakan_atasan'])) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Catatan -->
            <?php if (!empty($izin['catatan'])): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-sticky-note me-2 text-primary"></i>
                    Catatan
                </h5>
                <div class="bg-light p-3 rounded">
                    <?= nl2br(htmlspecialchars($izin['catatan'])) ?>
                </div>
            </div>
            <?php endif; ?>
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
                        <?= strtoupper(substr($izin['nama_panggilan'] ?? $izin['nama_lengkap'] ?? '?', 0, 1)) ?>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($izin['nama_panggilan'] ?? $izin['nama_lengkap'] ?? '-') ?></h5>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($izin['nik'] ?? '-') ?></p>
                </div>
                <hr>
                <div class="mb-2">
                    <i class="fas fa-briefcase text-primary me-2" style="width: 20px;"></i>
                    <strong>Jabatan:</strong> <?= htmlspecialchars($izin['jabatan'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-building text-primary me-2" style="width: 20px;"></i>
                    <strong>Departemen:</strong> <?= htmlspecialchars($izin['departemen'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-calendar-alt text-primary me-2" style="width: 20px;"></i>
                    <strong>Tanggal Masuk:</strong> <?= formatDate($izin['karyawan_tanggal_masuk'] ?? '') ?>
                </div>
            </div>

            <!-- Informasi Kontak Darurat -->
            <?php if (!empty($izin['alamat_selama_cuti']) || !empty($izin['no_telepon_cuti'])): ?>
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-phone-alt me-2 text-primary"></i>
                    Kontak Darurat
                </h5>
                <?php if (!empty($izin['alamat_selama_cuti'])): ?>
                <div class="mb-2">
                    <i class="fas fa-map-marker-alt text-primary me-2" style="width: 20px;"></i>
                    <strong>Alamat:</strong><br>
                    <span class="ms-4"><?= nl2br(htmlspecialchars($izin['alamat_selama_cuti'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($izin['no_telepon_cuti'])): ?>
                <div class="mb-2">
                    <i class="fas fa-phone text-primary me-2" style="width: 20px;"></i>
                    <strong>No. Telepon:</strong> <?= htmlspecialchars($izin['no_telepon_cuti']) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <?php if ($isPending): ?>
            <div class="modern-card mt-4">
                <h5 class="mb-3">
                    <i class="fas fa-bolt me-2 text-primary"></i>
                    Tindakan Cepat
                </h5>
                <div class="d-grid gap-2">
                    <button class="btn btn-success approve-btn" data-id="<?= $izinId ?>" data-nomor="<?= htmlspecialchars($izin['nomor_izin'] ?? '') ?>" data-nama="<?= htmlspecialchars($izin['nama_panggilan'] ?? $izin['nama_lengkap'] ?? '') ?>">
                        <i class="fas fa-check me-2"></i>Setujui Izin
                    </button>
                    <button class="btn btn-danger reject-btn" data-id="<?= $izinId ?>" data-nomor="<?= htmlspecialchars($izin['nomor_izin'] ?? '') ?>" data-nama="<?= htmlspecialchars($izin['nama_panggilan'] ?? $izin['nama_lengkap'] ?? '') ?>">
                        <i class="fas fa-times me-2"></i>Tolak Izin
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
                    Tolak Pengajuan Izin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejectId">
                    <div class="mb-3">
                        <label class="form-label">Nomor Izin</label>
                        <p class="fw-bold" id="rejectNomor"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Karyawan</label>
                        <p class="fw-bold" id="rejectNama"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasanPenolakan" class="form-control" rows="4" 
                                  placeholder="Isikan alasan penolakan pengajuan izin..."></textarea>
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
        var nomor = $(this).data('nomor');
        var nama = $(this).data('nama');
        
        Swal.fire({
            title: 'Konfirmasi',
            html: 'Setujui pengajuan izin <strong>' + nomor + '</strong><br>atas nama <strong>' + nama + '</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/izin/approve') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/izin') ?>';
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
        var nomor = $(this).data('nomor');
        var nama = $(this).data('nama');
        
        $('#rejectId').val(id);
        $('#rejectNomor').text(nomor);
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
            text: 'Tolak pengajuan izin ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/izin/reject') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/izin') ?>';
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
</style>