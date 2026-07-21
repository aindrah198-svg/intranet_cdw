<?php
// app/Views/direktur/approval/spk_detail.php

$spk = $spk ?? [];
$spkId = $spk['id'] ?? 0;
$isPending = ($spk['status'] ?? '') === 'draft';

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

// Helper function untuk format currency
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        if (empty($amount) || $amount == 0) {
            return '-';
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

// Helper function untuk badge status SPK
if (!function_exists('getSpkStatusBadge')) {
    function getSpkStatusBadge($status) {
        $badges = [
            'draft' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Draft</span>',
            'disetujui' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Disetujui</span>',
            'ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>',
            'on_progress' => '<span class="badge bg-info"><i class="fas fa-spinner me-1"></i>On Progress</span>',
            'selesai' => '<span class="badge bg-primary"><i class="fas fa-check-circle me-1"></i>Selesai</span>',
            'batal' => '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>Batal</span>'
        ];
        return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
}
?>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient">
                <i class="fas fa-file-contract me-2"></i>Detail Surat Perintah Kerja (SPK)
            </h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/approval/spk') ?>" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </p>
        </div>
        <div>
            <a href="<?= base_url('direktur/approval/spk/print/' . $spkId) ?>" 
               class="btn btn-modern-outline me-2" target="_blank">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
            <?php if ($isPending): ?>
            <button type="button" class="btn btn-success approve-btn" data-id="<?= $spkId ?>" data-nomor="<?= htmlspecialchars($spk['nomor_spk'] ?? '') ?>">
                <i class="fas fa-check me-2"></i>Setujui SPK
            </button>
            <button type="button" class="btn btn-danger reject-btn" data-id="<?= $spkId ?>" data-nomor="<?= htmlspecialchars($spk['nomor_spk'] ?? '') ?>">
                <i class="fas fa-times me-2"></i>Tolak SPK
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Informasi SPK -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Informasi SPK
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Nomor SPK</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($spk['nomor_spk'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Dibuat</label>
                        <p class="mb-0"><?= formatDateTime($spk['created_at'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Status</label>
                        <p class="mb-0"><?= getSpkStatusBadge($spk['status'] ?? 'draft') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Project Terkait</label>
                        <p class="mb-0">
                            <?php if (!empty($spk['nama_project'])): ?>
                                <?= htmlspecialchars($spk['kode_project'] ?? '') ?> - <?= htmlspecialchars($spk['nama_project']) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Judul Pekerjaan</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($spk['judul_pekerjaan'] ?? '-') ?></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Deskripsi Pekerjaan</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($spk['deskripsi_pekerjaan'] ?? '-')) ?>
                        </div>
                    </div>
                    <?php if (!empty($spk['lokasi_pekerjaan'])): ?>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Lokasi Pekerjaan</label>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                            <?= htmlspecialchars($spk['lokasi_pekerjaan']) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detail Pekerjaan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                    Jadwal & Nilai Kontrak
                </h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Mulai</label>
                        <p class="fw-bold mb-0">
                            <i class="fas fa-play me-1 text-success"></i>
                            <?= formatDateIndo($spk['tanggal_mulai'] ?? '') ?>
                        </p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Selesai</label>
                        <p class="fw-bold mb-0">
                            <i class="fas fa-flag-checkered me-1 text-danger"></i>
                            <?= formatDateIndo($spk['tanggal_selesai'] ?? '') ?>
                        </p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small mb-1 d-block">Nilai Kontrak</label>
                        <p class="fw-bold mb-0 text-primary">
                            <?= formatCurrency($spk['nilai_kontrak'] ?? 0) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Penanggung Jawab -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-user-cog me-2 text-primary"></i>
                    Penanggung Jawab
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Nama</label>
                        <p class="fw-bold mb-0">
                            <i class="fas fa-user me-1"></i>
                            <?= htmlspecialchars($spk['penanggung_jawab_nama'] ?? '-') ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">NIK</label>
                        <p class="mb-0"><?= htmlspecialchars($spk['penanggung_jawab_nik'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Jabatan</label>
                        <p class="mb-0"><?= htmlspecialchars($spk['penanggung_jawab_jabatan'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">No. Telepon</label>
                        <p class="mb-0"><?= htmlspecialchars($spk['penanggung_jawab_telepon'] ?? '-') ?></p>
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            <?php if (!empty($spk['catatan'])): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-sticky-note me-2 text-primary"></i>
                    Catatan
                </h5>
                <div class="bg-light p-3 rounded">
                    <?= nl2br(htmlspecialchars($spk['catatan'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Informasi Client -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-building me-2 text-primary"></i>
                    Informasi Client
                </h5>
                <div class="mb-2">
                    <i class="fas fa-building text-primary me-2" style="width: 20px;"></i>
                    <strong>Perusahaan:</strong> <?= htmlspecialchars($spk['nama_perusahaan'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-tag text-primary me-2" style="width: 20px;"></i>
                    <strong>Kode Client:</strong> <?= htmlspecialchars($spk['kode_client'] ?? '-') ?>
                </div>
                <?php if (!empty($spk['client_alamat'])): ?>
                <div class="mb-2">
                    <i class="fas fa-map-marker-alt text-primary me-2" style="width: 20px;"></i>
                    <strong>Alamat:</strong><br>
                    <span class="ms-4"><?= nl2br(htmlspecialchars($spk['client_alamat'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($spk['client_telepon'])): ?>
                <div class="mb-2">
                    <i class="fas fa-phone text-primary me-2" style="width: 20px;"></i>
                    <strong>Telepon:</strong> <?= htmlspecialchars($spk['client_telepon']) ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($spk['client_email'])): ?>
                <div class="mb-2">
                    <i class="fas fa-envelope text-primary me-2" style="width: 20px;"></i>
                    <strong>Email:</strong> <?= htmlspecialchars($spk['client_email']) ?>
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
                    <strong>Nama:</strong> <?= htmlspecialchars($spk['created_by_name'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-briefcase text-primary me-2" style="width: 20px;"></i>
                    <strong>Jabatan:</strong> <?= htmlspecialchars($spk['created_by_jabatan'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-clock text-primary me-2" style="width: 20px;"></i>
                    <strong>Tanggal Buat:</strong> <?= formatDateTime($spk['created_at'] ?? '') ?>
                </div>
            </div>

            <!-- Informasi Approval -->
            <?php if (!empty($spk['approved_by_name'])): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-check-circle me-2 text-primary"></i>
                    Informasi Approval
                </h5>
                <div class="mb-2">
                    <i class="fas fa-user-check text-primary me-2" style="width: 20px;"></i>
                    <strong>Disetujui Oleh:</strong> <?= htmlspecialchars($spk['approved_by_name'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-briefcase text-primary me-2" style="width: 20px;"></i>
                    <strong>Jabatan:</strong> <?= htmlspecialchars($spk['approved_by_jabatan'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-calendar-check text-primary me-2" style="width: 20px;"></i>
                    <strong>Tanggal:</strong> <?= formatDateTime($spk['approved_at'] ?? '') ?>
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
                    <button class="btn btn-success approve-btn" data-id="<?= $spkId ?>" data-nomor="<?= htmlspecialchars($spk['nomor_spk'] ?? '') ?>">
                        <i class="fas fa-check me-2"></i>Setujui SPK
                    </button>
                    <button class="btn btn-danger reject-btn" data-id="<?= $spkId ?>" data-nomor="<?= htmlspecialchars($spk['nomor_spk'] ?? '') ?>">
                        <i class="fas fa-times me-2"></i>Tolak SPK
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
                    Tolak SPK
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejectId">
                    <div class="mb-3">
                        <label class="form-label">Nomor SPK</label>
                        <p class="fw-bold" id="rejectNomor"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasanPenolakan" class="form-control" rows="4" 
                                  placeholder="Isikan alasan penolakan SPK..."></textarea>
                        <small class="text-muted">Alasan akan dicatat sebagai catatan SPK</small>
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
            html: 'Setujui SPK <strong>' + nomor + '</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/spk/approve') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/spk') ?>';
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
            text: 'Tolak SPK ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/spk/reject') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/spk') ?>';
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