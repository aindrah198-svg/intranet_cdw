<?php
// app/Views/direktur/approval/bast_detail.php

$bast = $bast ?? [];
$bastId = $bast['id'] ?? 0;
$isPending = ($bast['status_direktur'] ?? '') === 'Menunggu' && ($bast['status_hrd'] ?? '') === 'Disetujui HRD';

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

// Helper function untuk badge status direktur
if (!function_exists('getDirekturStatusBadge')) {
    function getDirekturStatusBadge($status) {
        $badges = [
            'Menunggu' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>',
            'Disetujui' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>'
        ];
        return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
}

// Helper function untuk badge kondisi
if (!function_exists('getKondisiBadge')) {
    function getKondisiBadge($kondisi) {
        $badges = [
            'Baik' => '<span class="badge bg-success">Baik</span>',
            'Cukup' => '<span class="badge bg-warning text-dark">Cukup</span>',
            'Perlu Perbaikan' => '<span class="badge bg-danger">Perlu Perbaikan</span>'
        ];
        return $badges[$kondisi] ?? '<span class="badge bg-secondary">' . $kondisi . '</span>';
    }
}
?>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient">
                <i class="fas fa-file-signature me-2"></i>Detail Berita Acara Serah Terima (BAST)
            </h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/approval/bast') ?>" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </p>
        </div>
        <div>
            <a href="<?= base_url('direktur/approval/bast/print/' . $bastId) ?>" 
               class="btn btn-modern-outline me-2" target="_blank">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
            <?php if ($isPending): ?>
            <button type="button" class="btn btn-success approve-btn" data-id="<?= $bastId ?>" data-nomor="<?= htmlspecialchars($bast['nomor_bast'] ?? '') ?>">
                <i class="fas fa-check me-2"></i>Setujui BAST
            </button>
            <button type="button" class="btn btn-danger reject-btn" data-id="<?= $bastId ?>" data-nomor="<?= htmlspecialchars($bast['nomor_bast'] ?? '') ?>">
                <i class="fas fa-times me-2"></i>Tolak BAST
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Informasi BAST -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Informasi BAST
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Nomor BAST</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($bast['nomor_bast'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal BAST</label>
                        <p class="mb-0"><?= formatDateIndo($bast['tanggal_bast'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Status BAST</label>
                        <p class="mb-0"><?= getDirekturStatusBadge($bast['status_direktur'] ?? 'Menunggu') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Kondisi Pekerjaan</label>
                        <p class="mb-0"><?= getKondisiBadge($bast['kondisi'] ?? 'Baik') ?></p>
                    </div>
                </div>
            </div>

            <!-- Informasi Client & Project -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-briefcase me-2 text-primary"></i>
                    Informasi Client & Project
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Client</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($bast['nama_perusahaan'] ?? '-') ?></p>
                        <small class="text-muted">Kode: <?= htmlspecialchars($bast['kode_client'] ?? '-') ?></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">No. SPK</label>
                        <p class="mb-0"><?= htmlspecialchars($bast['nomor_spk'] ?? '-') ?></p>
                    </div>
                    <?php if (!empty($bast['nomor_surat_jalan'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">No. Surat Jalan</label>
                        <p class="mb-0"><?= htmlspecialchars($bast['nomor_surat_jalan']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($bast['nilai_kontrak'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Nilai Kontrak</label>
                        <p class="fw-bold text-primary mb-0"><?= 'Rp ' . number_format($bast['nilai_kontrak'], 0, ',', '.') ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detail Pekerjaan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>
                    Detail Pekerjaan
                </h5>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Judul Pekerjaan</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($bast['judul_pekerjaan'] ?? '-') ?></p>
                    </div>
                    <?php if (!empty($bast['lokasi_pekerjaan'])): ?>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Lokasi Pekerjaan</label>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                            <?= htmlspecialchars($bast['lokasi_pekerjaan']) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Deskripsi Pekerjaan</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($bast['deskripsi_pekerjaan'] ?? '-')) ?>
                        </div>
                    </div>
                    <?php if (!empty($bast['catatan_tambahan'])): ?>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Catatan Tambahan</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($bast['catatan_tambahan'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pihak Penyerah dan Penerima -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-handshake me-2 text-primary"></i>
                    Pihak Penyerah dan Penerima
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-3">Pihak Pertama (Penyerah)</h6>
                            <div class="mb-2">
                                <strong>Nama:</strong> <?= htmlspecialchars($bast['pihak_pertama_nama'] ?? '-') ?>
                            </div>
                            <div class="mb-2">
                                <strong>Jabatan:</strong> <?= htmlspecialchars($bast['pihak_pertama_jabatan'] ?? '-') ?>
                            </div>
                            <?php if (!empty($bast['pihak_pertama_tanggal'])): ?>
                            <div class="mb-2">
                                <strong>Tanggal:</strong> <?= formatDateIndo($bast['pihak_pertama_tanggal']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-3">Pihak Kedua (Penerima)</h6>
                            <div class="mb-2">
                                <strong>Nama:</strong> <?= htmlspecialchars($bast['pihak_kedua_nama'] ?? '-') ?>
                            </div>
                            <div class="mb-2">
                                <strong>Jabatan:</strong> <?= htmlspecialchars($bast['pihak_kedua_jabatan'] ?? '-') ?>
                            </div>
                            <?php if (!empty($bast['pihak_kedua_tanggal'])): ?>
                            <div class="mb-2">
                                <strong>Tanggal:</strong> <?= formatDateIndo($bast['pihak_kedua_tanggal']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dokumen Pendukung -->
            <?php if (!empty($bast['dokumen_pendukung'])): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-paperclip me-2 text-primary"></i>
                    Dokumen Pendukung
                </h5>
                <a href="<?= base_url($bast['dokumen_pendukung']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="fas fa-file-download me-1"></i> Lihat Dokumen
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Persetujuan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Status Persetujuan
                </h5>
                <div class="row">
                    <div class="col-12 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-users fa-2x mb-2 <?= ($bast['status_hrd'] ?? '') === 'Disetujui HRD' ? 'text-success' : (($bast['status_hrd'] ?? '') === 'Ditolak HRD' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Approval HRD</h6>
                            <?php if (($bast['status_hrd'] ?? '') === 'Disetujui HRD'): ?>
                                <span class="badge bg-success">Disetujui</span>
                                <?php if (!empty($bast['hrd_nama'])): ?>
                                    <small class="d-block mt-1">Oleh: <?= htmlspecialchars($bast['hrd_nama']) ?></small>
                                <?php endif; ?>
                            <?php elseif (($bast['status_hrd'] ?? '') === 'Ditolak HRD'): ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-crown fa-2x mb-2 <?= ($bast['status_direktur'] ?? '') === 'Disetujui' ? 'text-success' : (($bast['status_direktur'] ?? '') === 'Ditolak' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Approval Direktur</h6>
                            <?= getDirekturStatusBadge($bast['status_direktur'] ?? 'Menunggu') ?>
                            <?php if (!empty($bast['direktur_nama']) && ($bast['status_direktur'] ?? '') !== 'Menunggu'): ?>
                                <small class="d-block mt-1">Oleh: <?= htmlspecialchars($bast['direktur_nama']) ?></small>
                                <small class="d-block"><?= formatDateTime($bast['disetujui_direktur_at'] ?? '') ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-check-circle fa-2x mb-2 <?= ($bast['status_keseluruhan'] ?? '') === 'Selesai' || ($bast['status_keseluruhan'] ?? '') === 'Disetujui' ? 'text-success' : 'text-secondary' ?>"></i>
                            <h6 class="mb-1">Status Keseluruhan</h6>
                            <?php
                            $statusKeseluruhan = $bast['status_keseluruhan'] ?? 'Draft';
                            $badgeMap = [
                                'Draft' => 'secondary',
                                'Menunggu HRD' => 'info',
                                'Menunggu Direktur' => 'warning',
                                'Disetujui' => 'success',
                                'Ditolak' => 'danger',
                                'Selesai' => 'dark'
                            ];
                            ?>
                            <span class="badge bg-<?= $badgeMap[$statusKeseluruhan] ?? 'secondary' ?>">
                                <?= $statusKeseluruhan ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Client -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-building me-2 text-primary"></i>
                    Informasi Client
                </h5>
                <?php if (!empty($bast['client_alamat'])): ?>
                <div class="mb-2">
                    <i class="fas fa-map-marker-alt text-primary me-2" style="width: 20px;"></i>
                    <strong>Alamat:</strong><br>
                    <span class="ms-4"><?= nl2br(htmlspecialchars($bast['client_alamat'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($bast['client_telepon'])): ?>
                <div class="mb-2">
                    <i class="fas fa-phone text-primary me-2" style="width: 20px;"></i>
                    <strong>Telepon:</strong> <?= htmlspecialchars($bast['client_telepon']) ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($bast['client_email'])): ?>
                <div class="mb-2">
                    <i class="fas fa-envelope text-primary me-2" style="width: 20px;"></i>
                    <strong>Email:</strong> <?= htmlspecialchars($bast['client_email']) ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Informasi Pembuat -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-user-edit me-2 text-primary"></i>
                    Informasi Pembuat
                </h5>
                <div class="mb-2">
                    <i class="fas fa-user text-primary me-2" style="width: 20px;"></i>
                    <strong>Nama:</strong> <?= htmlspecialchars($bast['created_by_name'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-briefcase text-primary me-2" style="width: 20px;"></i>
                    <strong>Jabatan:</strong> <?= htmlspecialchars($bast['created_by_jabatan'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-clock text-primary me-2" style="width: 20px;"></i>
                    <strong>Tanggal Buat:</strong> <?= formatDateTime($bast['created_at'] ?? '') ?>
                </div>
            </div>

            <!-- Alasan Penolakan -->
            <?php if (!empty($bast['alasan_penolakan_direktur'])): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-danger"></i>
                    Alasan Penolakan
                </h5>
                <div class="bg-danger bg-opacity-10 p-3 rounded">
                    <i class="fas fa-ban me-1 text-danger"></i>
                    <?= nl2br(htmlspecialchars($bast['alasan_penolakan_direktur'])) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Catatan -->
            <?php if (!empty($bast['catatan'])): ?>
            <div class="modern-card mt-4">
                <h5 class="mb-3">
                    <i class="fas fa-sticky-note me-2 text-primary"></i>
                    Catatan
                </h5>
                <div class="bg-light p-3 rounded">
                    <?= nl2br(htmlspecialchars($bast['catatan'])) ?>
                </div>
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
                    <button class="btn btn-success approve-btn" data-id="<?= $bastId ?>" data-nomor="<?= htmlspecialchars($bast['nomor_bast'] ?? '') ?>">
                        <i class="fas fa-check me-2"></i>Setujui BAST
                    </button>
                    <button class="btn btn-danger reject-btn" data-id="<?= $bastId ?>" data-nomor="<?= htmlspecialchars($bast['nomor_bast'] ?? '') ?>">
                        <i class="fas fa-times me-2"></i>Tolak BAST
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
                    Tolak BAST
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejectId">
                    <div class="mb-3">
                        <label class="form-label">Nomor BAST</label>
                        <p class="fw-bold" id="rejectNomor"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasanPenolakan" class="form-control" rows="4" 
                                  placeholder="Isikan alasan penolakan BAST..."></textarea>
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
        
        Swal.fire({
            title: 'Konfirmasi',
            html: 'Setujui BAST <strong>' + nomor + '</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/bast/approve') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/bast') ?>';
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
        
        $('#rejectId').val(id);
        $('#rejectNomor').text(nomor);
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
            text: 'Tolak BAST ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/bast/reject') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/bast') ?>';
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