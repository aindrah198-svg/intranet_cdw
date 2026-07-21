<?php
// app/Views/direktur/approval/dokumen_detail.php

$dokumen = $dokumen ?? [];
$dokumenId = $dokumen['id'] ?? 0;
$isPending = ($dokumen['status_direktur'] ?? '') === 'Menunggu' && ($dokumen['status_hrd'] ?? '') === 'Diproses';

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

// Helper function untuk badge status keseluruhan
if (!function_exists('getKeseluruhanStatusBadge')) {
    function getKeseluruhanStatusBadge($status) {
        $badges = [
            'Draft' => '<span class="badge bg-secondary">Draft</span>',
            'Menunggu HRD' => '<span class="badge bg-info">Menunggu HRD</span>',
            'Menunggu Direktur' => '<span class="badge bg-warning text-dark">Menunggu Direktur</span>',
            'Diproses' => '<span class="badge bg-primary">Diproses</span>',
            'Selesai' => '<span class="badge bg-success">Selesai</span>',
            'Ditolak' => '<span class="badge bg-danger">Ditolak</span>'
        ];
        return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
}

// Helper function untuk badge jenis dokumen
if (!function_exists('getJenisDokumenBadge')) {
    function getJenisDokumenBadge($jenis) {
        $badges = [
            'SK Pengangkatan' => '<span class="badge bg-primary">SK Pengangkatan</span>',
            'SK Mutasi' => '<span class="badge bg-info">SK Mutasi</span>',
            'SK Pemberhentian' => '<span class="badge bg-danger">SK Pemberhentian</span>',
            'Surat Keterangan Kerja' => '<span class="badge bg-success">Surat Keterangan Kerja</span>',
            'Surat Keterangan Gaji' => '<span class="badge bg-success">Surat Keterangan Gaji</span>',
            'Surat Referensi' => '<span class="badge bg-secondary">Surat Referensi</span>',
            'Copy Kontrak Kerja' => '<span class="badge bg-dark">Copy Kontrak Kerja</span>',
            'Copy Dokumen Lain' => '<span class="badge bg-secondary">Copy Dokumen Lain</span>',
            'Legalitas Perusahaan' => '<span class="badge bg-warning text-dark">Legalitas Perusahaan</span>',
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
                <i class="fas fa-file-alt me-2"></i>Detail Pengajuan Dokumen
            </h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/approval/dokumen') ?>" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </p>
        </div>
        <div>
            <a href="<?= base_url('direktur/approval/dokumen/print/' . $dokumenId) ?>" 
               class="btn btn-modern-outline me-2" target="_blank">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
            <?php if ($isPending): ?>
            <button type="button" class="btn btn-success approve-btn" data-id="<?= $dokumenId ?>" data-nomor="<?= htmlspecialchars($dokumen['nomor_form'] ?? '') ?>" data-nama="<?= htmlspecialchars($dokumen['nama_panggilan'] ?? $dokumen['nama_lengkap'] ?? '') ?>">
                <i class="fas fa-check me-2"></i>Setujui Dokumen
            </button>
            <button type="button" class="btn btn-danger reject-btn" data-id="<?= $dokumenId ?>" data-nomor="<?= htmlspecialchars($dokumen['nomor_form'] ?? '') ?>" data-nama="<?= htmlspecialchars($dokumen['nama_panggilan'] ?? $dokumen['nama_lengkap'] ?? '') ?>">
                <i class="fas fa-times me-2"></i>Tolak Dokumen
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
                        <label class="text-muted small mb-1 d-block">Nomor Form</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($dokumen['nomor_form'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Pengajuan</label>
                        <p class="mb-0"><?= formatDateTime($dokumen['tanggal_pengajuan'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Jenis Dokumen</label>
                        <p class="mb-0"><?= getJenisDokumenBadge($dokumen['jenis_dokumen'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Status Pengajuan</label>
                        <p class="mb-0"><?= getKeseluruhanStatusBadge($dokumen['status_keseluruhan'] ?? 'Draft') ?></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Keperluan</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($dokumen['keperluan'] ?? '-')) ?>
                        </div>
                    </div>
                    <?php if (!empty($dokumen['keterangan_tambahan'])): ?>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Keterangan Tambahan</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($dokumen['keterangan_tambahan'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Pengajuan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Status Persetujuan
                </h5>
                <div class="row">
                    <div class="col-md-4 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-users fa-2x mb-2 <?= ($dokumen['status_hrd'] ?? '') === 'Selesai' ? 'text-success' : (($dokumen['status_hrd'] ?? '') === 'Diproses' ? 'text-primary' : (($dokumen['status_hrd'] ?? '') === 'Ditolak' ? 'text-danger' : 'text-warning')) ?>"></i>
                            <h6 class="mb-1">Status HRD</h6>
                            <?php if (($dokumen['status_hrd'] ?? '') === 'Selesai'): ?>
                                <span class="badge bg-success">Selesai</span>
                                <?php if (!empty($dokumen['hrd_nama'])): ?>
                                    <small class="d-block mt-1">Oleh: <?= htmlspecialchars($dokumen['hrd_nama']) ?></small>
                                <?php endif; ?>
                            <?php elseif (($dokumen['status_hrd'] ?? '') === 'Diproses'): ?>
                                <span class="badge bg-primary">Diproses</span>
                            <?php elseif (($dokumen['status_hrd'] ?? '') === 'Ditolak'): ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            <?php endif; ?>
                            <?php if (!empty($dokumen['approved_at_hrd'])): ?>
                                <small class="d-block mt-1"><?= formatDateTime($dokumen['approved_at_hrd']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-crown fa-2x mb-2 <?= ($dokumen['status_direktur'] ?? '') === 'Disetujui' ? 'text-success' : (($dokumen['status_direktur'] ?? '') === 'Ditolak' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Status Direktur</h6>
                            <?= getDirekturStatusBadge($dokumen['status_direktur'] ?? 'Menunggu') ?>
                            <?php if (!empty($dokumen['direktur_nama']) && ($dokumen['status_direktur'] ?? '') !== 'Menunggu'): ?>
                                <small class="d-block mt-1">Oleh: <?= htmlspecialchars($dokumen['direktur_nama']) ?></small>
                                <small class="d-block"><?= formatDateTime($dokumen['approved_at_direktur'] ?? '') ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-file-alt fa-2x mb-2 <?= ($dokumen['status_keseluruhan'] ?? '') === 'Selesai' ? 'text-success' : 'text-secondary' ?>"></i>
                            <h6 class="mb-1">Dokumen Hasil</h6>
                            <?php if (!empty($dokumen['dokumen_hasil_path'])): ?>
                                <a href="<?= base_url($dokumen['dokumen_hasil_path']) ?>" class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                                    <i class="fas fa-download me-1"></i> Lihat Dokumen
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Belum tersedia</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Pembuat -->
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-user-edit me-2 text-primary"></i>
                    Informasi Pembuat
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Nama</label>
                        <p class="mb-0"><?= htmlspecialchars($dokumen['created_by_name'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Jabatan</label>
                        <p class="mb-0"><?= htmlspecialchars($dokumen['created_by_jabatan'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Buat</label>
                        <p class="mb-0"><?= formatDateTime($dokumen['created_at'] ?? '') ?></p>
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
                        <?= strtoupper(substr($dokumen['nama_panggilan'] ?? $dokumen['nama_lengkap'] ?? '?', 0, 1)) ?>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($dokumen['nama_panggilan'] ?? $dokumen['nama_lengkap'] ?? '-') ?></h5>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($dokumen['nik'] ?? '-') ?></p>
                </div>
                <hr>
                <div class="mb-2">
                    <i class="fas fa-briefcase text-primary me-2" style="width: 20px;"></i>
                    <strong>Jabatan:</strong> <?= htmlspecialchars($dokumen['jabatan'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-building text-primary me-2" style="width: 20px;"></i>
                    <strong>Departemen:</strong> <?= htmlspecialchars($dokumen['departemen'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-calendar-alt text-primary me-2" style="width: 20px;"></i>
                    <strong>Tanggal Masuk:</strong> <?= formatDate($dokumen['karyawan_tanggal_masuk'] ?? '') ?>
                </div>
            </div>

            <!-- Alasan Penolakan -->
            <?php if (!empty($dokumen['alasan_penolakan'])): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-danger"></i>
                    Alasan Penolakan
                </h5>
                <div class="bg-danger bg-opacity-10 p-3 rounded">
                    <i class="fas fa-ban me-1 text-danger"></i>
                    <?= nl2br(htmlspecialchars($dokumen['alasan_penolakan'])) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Catatan -->
            <?php if (!empty($dokumen['catatan'])): ?>
            <div class="modern-card mt-4">
                <h5 class="mb-3">
                    <i class="fas fa-sticky-note me-2 text-primary"></i>
                    Catatan
                </h5>
                <div class="bg-light p-3 rounded">
                    <?= nl2br(htmlspecialchars($dokumen['catatan'])) ?>
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
                    <button class="btn btn-success approve-btn" data-id="<?= $dokumenId ?>" data-nomor="<?= htmlspecialchars($dokumen['nomor_form'] ?? '') ?>" data-nama="<?= htmlspecialchars($dokumen['nama_panggilan'] ?? $dokumen['nama_lengkap'] ?? '') ?>">
                        <i class="fas fa-check me-2"></i>Setujui Dokumen
                    </button>
                    <button class="btn btn-danger reject-btn" data-id="<?= $dokumenId ?>" data-nomor="<?= htmlspecialchars($dokumen['nomor_form'] ?? '') ?>" data-nama="<?= htmlspecialchars($dokumen['nama_panggilan'] ?? $dokumen['nama_lengkap'] ?? '') ?>">
                        <i class="fas fa-times me-2"></i>Tolak Dokumen
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
                    Tolak Pengajuan Dokumen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejectId">
                    <div class="mb-3">
                        <label class="form-label">Nomor Form</label>
                        <p class="fw-bold" id="rejectNomor"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Karyawan</label>
                        <p class="fw-bold" id="rejectNama"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasanPenolakan" class="form-control" rows="4" 
                                  placeholder="Isikan alasan penolakan pengajuan dokumen..."></textarea>
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
            html: 'Setujui pengajuan dokumen <strong>' + nomor + '</strong><br>atas nama <strong>' + nama + '</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/dokumen/approve') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/dokumen') ?>';
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
            text: 'Tolak pengajuan dokumen ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/dokumen/reject') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/dokumen') ?>';
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