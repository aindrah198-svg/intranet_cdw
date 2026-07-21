<?php
// app/Views/direktur/approval/kasbon_detail.php

$kasbon = $kasbon ?? [];
$kasbonId = $kasbon['id'] ?? 0;
$isPending = ($kasbon['status_direktur'] ?? '') === 'Menunggu' && ($kasbon['status_hrd'] ?? '') === 'Disetujui HRD';

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
            'Disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            'Dicairkan' => '<span class="badge bg-primary">Dicairkan</span>',
            'Lunas' => '<span class="badge bg-dark">Lunas</span>'
        ];
        return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
}

// Helper function untuk badge metode pencairan
if (!function_exists('getMetodePencairanBadge')) {
    function getMetodePencairanBadge($metode) {
        $badges = [
            'transfer' => '<span class="badge bg-info"><i class="fas fa-university me-1"></i>Transfer Bank</span>',
            'tunai' => '<span class="badge bg-success"><i class="fas fa-money-bill-wave me-1"></i>Tunai</span>'
        ];
        return $badges[$metode] ?? '<span class="badge bg-secondary">' . $metode . '</span>';
    }
}
?>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient">
                <i class="fas fa-money-bill-wave me-2"></i>Detail Pengajuan Kasbon
            </h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/approval/kasbon') ?>" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </p>
        </div>
        <div>
            <a href="<?= base_url('direktur/approval/kasbon/print/' . $kasbonId) ?>" 
               class="btn btn-modern-outline me-2" target="_blank">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
            <?php if ($isPending): ?>
            <button type="button" class="btn btn-success approve-btn" data-id="<?= $kasbonId ?>" data-nomor="<?= htmlspecialchars($kasbon['nomor_kasbon'] ?? '') ?>" data-nama="<?= htmlspecialchars($kasbon['nama_panggilan'] ?? $kasbon['nama_lengkap'] ?? '') ?>">
                <i class="fas fa-check me-2"></i>Setujui Kasbon
            </button>
            <button type="button" class="btn btn-danger reject-btn" data-id="<?= $kasbonId ?>" data-nomor="<?= htmlspecialchars($kasbon['nomor_kasbon'] ?? '') ?>" data-nama="<?= htmlspecialchars($kasbon['nama_panggilan'] ?? $kasbon['nama_lengkap'] ?? '') ?>">
                <i class="fas fa-times me-2"></i>Tolak Kasbon
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
                        <label class="text-muted small mb-1 d-block">Nomor Kasbon</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($kasbon['nomor_kasbon'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Pengajuan</label>
                        <p class="mb-0"><?= formatDateTime($kasbon['tanggal_pengajuan'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Jumlah Kasbon</label>
                        <p class="fw-bold mb-0 text-primary"><?= formatCurrency($kasbon['jumlah_kasbon'] ?? 0) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Dibutuhkan</label>
                        <p class="mb-0">
                            <?= !empty($kasbon['tanggal_dibutuhkan']) ? formatDateIndo($kasbon['tanggal_dibutuhkan']) : '-' ?>
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Alasan Kasbon</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($kasbon['alasan'] ?? '-')) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Rencana Pelunasan</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($kasbon['rencana_pelunasan'] ?? '-')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Pengajuan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Status Pengajuan
                </h5>
                <div class="row">
                    <div class="col-md-4 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-users fa-2x mb-2 <?= ($kasbon['status_hrd'] ?? '') === 'Disetujui HRD' ? 'text-success' : (($kasbon['status_hrd'] ?? '') === 'Ditolak HRD' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Approval HRD</h6>
                            <?php if (($kasbon['status_hrd'] ?? '') === 'Disetujui HRD'): ?>
                                <span class="badge bg-success">Disetujui</span>
                                <?php if (!empty($kasbon['hrd_nama'])): ?>
                                    <small class="d-block mt-1">Oleh: <?= htmlspecialchars($kasbon['hrd_nama']) ?></small>
                                <?php endif; ?>
                            <?php elseif (($kasbon['status_hrd'] ?? '') === 'Ditolak HRD'): ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-crown fa-2x mb-2 <?= ($kasbon['status_direktur'] ?? '') === 'Disetujui' ? 'text-success' : (($kasbon['status_direktur'] ?? '') === 'Ditolak' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Approval Direktur</h6>
                            <?= getDirekturStatusBadge($kasbon['status_direktur'] ?? 'Menunggu') ?>
                            <?php if (!empty($kasbon['direktur_nama']) && ($kasbon['status_direktur'] ?? '') !== 'Menunggu'): ?>
                                <small class="d-block mt-1">Oleh: <?= htmlspecialchars($kasbon['direktur_nama']) ?></small>
                                <small class="d-block"><?= formatDateTime($kasbon['disetujui_direktur_at'] ?? '') ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-hand-holding-usd fa-2x mb-2 <?= ($kasbon['status_keseluruhan'] ?? '') === 'Lunas' ? 'text-success' : (($kasbon['status_keseluruhan'] ?? '') === 'Dicairkan' ? 'text-primary' : 'text-secondary') ?>"></i>
                            <h6 class="mb-1">Status Akhir</h6>
                            <?= getKeseluruhanStatusBadge($kasbon['status_keseluruhan'] ?? 'Draft') ?>
                            <?php if (($kasbon['status_keseluruhan'] ?? '') === 'Dicairkan' && !empty($kasbon['tanggal_pencairan'])): ?>
                                <small class="d-block mt-1">Dicairkan: <?= formatDateIndo($kasbon['tanggal_pencairan']) ?></small>
                            <?php endif; ?>
                            <?php if (($kasbon['status_keseluruhan'] ?? '') === 'Lunas' && !empty($kasbon['lunas_pada'])): ?>
                                <small class="d-block mt-1">Lunas: <?= formatDateIndo($kasbon['lunas_pada']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Pencairan -->
            <?php if (!empty($kasbon['tanggal_pencairan']) || !empty($kasbon['metode_pencairan'])): ?>
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-university me-2 text-primary"></i>
                    Informasi Pencairan
                </h5>
                <div class="row">
                    <?php if (!empty($kasbon['tanggal_pencairan'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Pencairan</label>
                        <p class="mb-0"><?= formatDateIndo($kasbon['tanggal_pencairan']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($kasbon['metode_pencairan'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Metode Pencairan</label>
                        <p class="mb-0"><?= getMetodePencairanBadge($kasbon['metode_pencairan']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($kasbon['bank_tujuan'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Bank Tujuan</label>
                        <p class="mb-0"><?= htmlspecialchars($kasbon['bank_tujuan']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($kasbon['no_rekening_tujuan'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">No. Rekening Tujuan</label>
                        <p class="mb-0"><?= htmlspecialchars($kasbon['no_rekening_tujuan']) ?></p>
                    </div>
                    <?php endif; ?>
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
                        <?= strtoupper(substr($kasbon['nama_panggilan'] ?? $kasbon['nama_lengkap'] ?? '?', 0, 1)) ?>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($kasbon['nama_panggilan'] ?? $kasbon['nama_lengkap'] ?? '-') ?></h5>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($kasbon['nik'] ?? '-') ?></p>
                </div>
                <hr>
                <div class="mb-2">
                    <i class="fas fa-briefcase text-primary me-2" style="width: 20px;"></i>
                    <strong>Jabatan:</strong> <?= htmlspecialchars($kasbon['jabatan'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-building text-primary me-2" style="width: 20px;"></i>
                    <strong>Departemen:</strong> <?= htmlspecialchars($kasbon['departemen'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-calendar-alt text-primary me-2" style="width: 20px;"></i>
                    <strong>Tanggal Masuk:</strong> <?= formatDate($kasbon['karyawan_tanggal_masuk'] ?? '') ?>
                </div>
                <?php if (!empty($kasbon['gaji_pokok'])): ?>
                <div class="mb-2">
                    <i class="fas fa-money-bill-alt text-primary me-2" style="width: 20px;"></i>
                    <strong>Gaji Pokok:</strong> <?= formatCurrency($kasbon['gaji_pokok']) ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sisa Pinjaman -->
            <?php if (!empty($kasbon['sisa_pinjaman']) && $kasbon['sisa_pinjaman'] > 0): ?>
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>
                    Informasi Pinjaman
                </h5>
                <div class="text-center">
                    <div class="progress mb-3" style="height: 20px;">
                        <?php 
                        $total = $kasbon['jumlah_kasbon'] ?? 1;
                        $sisa = $kasbon['sisa_pinjaman'] ?? 0;
                        $persenLunas = (($total - $sisa) / $total) * 100;
                        ?>
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $persenLunas ?>%;" 
                             aria-valuenow="<?= $persenLunas ?>" aria-valuemin="0" aria-valuemax="100">
                            <?= round($persenLunas) ?>%
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Total Pinjaman</small>
                            <h6 class="mb-0"><?= formatCurrency($total) ?></h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Sisa Pinjaman</small>
                            <h6 class="mb-0 text-danger"><?= formatCurrency($sisa) ?></h6>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Alasan Penolakan -->
            <?php if (!empty($kasbon['alasan_penolakan_direktur'])): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-danger"></i>
                    Alasan Penolakan
                </h5>
                <div class="bg-danger bg-opacity-10 p-3 rounded">
                    <i class="fas fa-ban me-1 text-danger"></i>
                    <?= nl2br(htmlspecialchars($kasbon['alasan_penolakan_direktur'])) ?>
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
                    <button class="btn btn-success approve-btn" data-id="<?= $kasbonId ?>" data-nomor="<?= htmlspecialchars($kasbon['nomor_kasbon'] ?? '') ?>" data-nama="<?= htmlspecialchars($kasbon['nama_panggilan'] ?? $kasbon['nama_lengkap'] ?? '') ?>">
                        <i class="fas fa-check me-2"></i>Setujui Kasbon
                    </button>
                    <button class="btn btn-danger reject-btn" data-id="<?= $kasbonId ?>" data-nomor="<?= htmlspecialchars($kasbon['nomor_kasbon'] ?? '') ?>" data-nama="<?= htmlspecialchars($kasbon['nama_panggilan'] ?? $kasbon['nama_lengkap'] ?? '') ?>">
                        <i class="fas fa-times me-2"></i>Tolak Kasbon
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
                    Tolak Pengajuan Kasbon
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejectId">
                    <div class="mb-3">
                        <label class="form-label">Nomor Kasbon</label>
                        <p class="fw-bold" id="rejectNomor"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Karyawan</label>
                        <p class="fw-bold" id="rejectNama"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasanPenolakan" class="form-control" rows="4" 
                                  placeholder="Isikan alasan penolakan pengajuan kasbon..."></textarea>
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
            html: 'Setujui pengajuan kasbon <strong>' + nomor + '</strong><br>atas nama <strong>' + nama + '</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/kasbon/approve') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/kasbon') ?>';
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
            text: 'Tolak pengajuan kasbon ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/kasbon/reject') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/kasbon') ?>';
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
.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}
.progress-bar {
    border-radius: 10px;
    transition: width 0.3s ease;
}
</style>