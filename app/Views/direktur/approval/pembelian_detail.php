<?php
// app/Views/direktur/approval/pembelian_detail.php

$pembelian = $pembelian ?? [];
$items = $items ?? [];
$pembelianId = $pembelian['id'] ?? 0;
$isPending = ($pembelian['status_direktur'] ?? '') === 'Menunggu' && ($pembelian['status_hrd'] ?? '') === 'Disetujui HRD';

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

// Helper function untuk badge prioritas
if (!function_exists('getPrioritasBadge')) {
    function getPrioritasBadge($prioritas) {
        $badges = [
            'Rendah' => '<span class="badge bg-secondary">Rendah</span>',
            'Normal' => '<span class="badge bg-info">Normal</span>',
            'Tinggi' => '<span class="badge bg-warning text-dark">Tinggi</span>',
            'Urgent' => '<span class="badge bg-danger">Urgent</span>'
        ];
        return $badges[$prioritas] ?? '<span class="badge bg-secondary">' . $prioritas . '</span>';
    }
}

// Helper function untuk badge status penerimaan
if (!function_exists('getStatusPenerimaanBadge')) {
    function getStatusPenerimaanBadge($status) {
        $badges = [
            'Belum' => '<span class="badge bg-secondary">Belum Diterima</span>',
            'Sebagian' => '<span class="badge bg-warning text-dark">Sebagian</span>',
            'Lengkap' => '<span class="badge bg-success">Lengkap</span>'
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
                <i class="fas fa-shopping-cart me-2"></i>Detail Purchase Request (PR)
            </h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/approval/pembelian') ?>" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </p>
        </div>
        <div>
            <a href="<?= base_url('direktur/approval/pembelian/print/' . $pembelianId) ?>" 
               class="btn btn-modern-outline me-2" target="_blank">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
            <?php if ($isPending): ?>
            <button type="button" class="btn btn-success approve-btn" data-id="<?= $pembelianId ?>" data-nomor="<?= htmlspecialchars($pembelian['nomor_pr'] ?? '') ?>" data-nama="<?= htmlspecialchars($pembelian['nama_panggilan'] ?? $pembelian['nama_lengkap'] ?? '') ?>">
                <i class="fas fa-check me-2"></i>Setujui PR
            </button>
            <button type="button" class="btn btn-danger reject-btn" data-id="<?= $pembelianId ?>" data-nomor="<?= htmlspecialchars($pembelian['nomor_pr'] ?? '') ?>" data-nama="<?= htmlspecialchars($pembelian['nama_panggilan'] ?? $pembelian['nama_lengkap'] ?? '') ?>">
                <i class="fas fa-times me-2"></i>Tolak PR
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Informasi Purchase Request -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Informasi Purchase Request
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Nomor PR</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($pembelian['nomor_pr'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Pengajuan</label>
                        <p class="mb-0"><?= formatDateTime($pembelian['tanggal_pengajuan'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Prioritas</label>
                        <p class="mb-0"><?= getPrioritasBadge($pembelian['prioritas'] ?? 'Normal') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Dibutuhkan</label>
                        <p class="mb-0">
                            <?= !empty($pembelian['tanggal_dibutuhkan']) ? formatDateIndo($pembelian['tanggal_dibutuhkan']) : '-' ?>
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Alasan Pembelian</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($pembelian['alasan_pembelian'] ?? '-')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Item -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-boxes me-2 text-primary"></i>
                    Daftar Item yang Dibeli
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Barang</th>
                                <th>Spesifikasi</th>
                                <th width="100">Qty</th>
                                <th width="80">Satuan</th>
                                <th width="150">Harga Estimasi</th>
                                <th width="150">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-box-open fa-2x text-muted mb-2 d-block"></i>
                                    Tidak ada item
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; $grandTotal = 0; ?>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($item['nama_barang']) ?></strong>
                                    </td>
                                    <td>
                                        <?= nl2br(htmlspecialchars($item['spesifikasi'] ?? '-')) ?>
                                    </td>
                                    <td class="text-center">
                                        <?= number_format($item['qty'], 2) ?>
                                    </td>
                                    <td class="text-center">
                                        <?= htmlspecialchars($item['satuan'] ?? 'pcs') ?>
                                    </td>
                                    <td class="text-end">
                                        <?= formatCurrency($item['estimasi_harga'] ?? 0) ?>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <?= formatCurrency($item['total_estimasi'] ?? ($item['qty'] * $item['estimasi_harga'])) ?>
                                    </td>
                                </tr>
                                <?php 
                                    $grandTotal += ($item['total_estimasi'] ?? ($item['qty'] * $item['estimasi_harga']));
                                endforeach; ?>
                                <tr class="table-active">
                                    <td colspan="6" class="text-end fw-bold">GRAND TOTAL</td>
                                    <td class="text-end fw-bold text-primary"><?= formatCurrency($grandTotal) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Informasi Supplier & PO -->
            <?php if (!empty($pembelian['supplier']) || !empty($pembelian['no_po_dibuat']) || !empty($pembelian['tanggal_pemesanan'])): ?>
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-truck me-2 text-primary"></i>
                    Informasi Supplier & Pemesanan
                </h5>
                <div class="row">
                    <?php if (!empty($pembelian['supplier'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Supplier</label>
                        <p class="mb-0 fw-bold"><?= htmlspecialchars($pembelian['supplier']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($pembelian['no_po_dibuat'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">No. PO</label>
                        <p class="mb-0"><?= htmlspecialchars($pembelian['no_po_dibuat']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($pembelian['tanggal_pemesanan'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Pemesanan</label>
                        <p class="mb-0"><?= formatDateIndo($pembelian['tanggal_pemesanan']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($pembelian['tanggal_terima'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Penerimaan</label>
                        <p class="mb-0"><?= formatDateIndo($pembelian['tanggal_terima']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($pembelian['status_penerimaan'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Status Penerimaan</label>
                        <p class="mb-0"><?= getStatusPenerimaanBadge($pembelian['status_penerimaan']) ?></p>
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
                    Informasi Pengaju
                </h5>
                <div class="text-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" 
                         style="width: 80px; height: 80px; font-size: 32px;">
                        <?= strtoupper(substr($pembelian['nama_panggilan'] ?? $pembelian['nama_lengkap'] ?? '?', 0, 1)) ?>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($pembelian['nama_panggilan'] ?? $pembelian['nama_lengkap'] ?? '-') ?></h5>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($pembelian['nik'] ?? '-') ?></p>
                </div>
                <hr>
                <div class="mb-2">
                    <i class="fas fa-briefcase text-primary me-2" style="width: 20px;"></i>
                    <strong>Jabatan:</strong> <?= htmlspecialchars($pembelian['jabatan'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-building text-primary me-2" style="width: 20px;"></i>
                    <strong>Departemen:</strong> <?= htmlspecialchars($pembelian['departemen'] ?? '-') ?>
                </div>
                <div class="mb-2">
                    <i class="fas fa-calendar-alt text-primary me-2" style="width: 20px;"></i>
                    <strong>Tanggal Masuk:</strong> <?= formatDate($pembelian['karyawan_tanggal_masuk'] ?? '') ?>
                </div>
            </div>

            <!-- Status Pengajuan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Status Pengajuan
                </h5>
                <div class="row">
                    <div class="col-12 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-users fa-2x mb-2 <?= ($pembelian['status_hrd'] ?? '') === 'Disetujui HRD' ? 'text-success' : (($pembelian['status_hrd'] ?? '') === 'Ditolak HRD' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Approval HRD</h6>
                            <?php if (($pembelian['status_hrd'] ?? '') === 'Disetujui HRD'): ?>
                                <span class="badge bg-success">Disetujui</span>
                                <?php if (!empty($pembelian['hrd_nama'])): ?>
                                    <small class="d-block mt-1">Oleh: <?= htmlspecialchars($pembelian['hrd_nama']) ?></small>
                                <?php endif; ?>
                            <?php elseif (($pembelian['status_hrd'] ?? '') === 'Ditolak HRD'): ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-crown fa-2x mb-2 <?= ($pembelian['status_direktur'] ?? '') === 'Disetujui' ? 'text-success' : (($pembelian['status_direktur'] ?? '') === 'Ditolak' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Approval Direktur</h6>
                            <?= getDirekturStatusBadge($pembelian['status_direktur'] ?? 'Menunggu') ?>
                            <?php if (!empty($pembelian['direktur_nama']) && ($pembelian['status_direktur'] ?? '') !== 'Menunggu'): ?>
                                <small class="d-block mt-1">Oleh: <?= htmlspecialchars($pembelian['direktur_nama']) ?></small>
                                <small class="d-block"><?= formatDateTime($pembelian['disetujui_direktur_at'] ?? '') ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-check-circle fa-2x mb-2 <?= ($pembelian['status_keseluruhan'] ?? '') === 'Disetujui' || ($pembelian['status_keseluruhan'] ?? '') === 'Selesai' ? 'text-success' : 'text-secondary' ?>"></i>
                            <h6 class="mb-1">Status Keseluruhan</h6>
                            <?php
                            $statusKeseluruhan = $pembelian['status_keseluruhan'] ?? 'Draft';
                            $badgeMap = [
                                'Draft' => 'secondary',
                                'Menunggu HRD' => 'info',
                                'Menunggu Direktur' => 'warning',
                                'Disetujui' => 'success',
                                'Ditolak' => 'danger',
                                'Dipesan' => 'primary',
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

            <!-- Alasan Penolakan -->
            <?php if (!empty($pembelian['alasan_penolakan_direktur'])): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-danger"></i>
                    Alasan Penolakan
                </h5>
                <div class="bg-danger bg-opacity-10 p-3 rounded">
                    <i class="fas fa-ban me-1 text-danger"></i>
                    <?= nl2br(htmlspecialchars($pembelian['alasan_penolakan_direktur'])) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Catatan -->
            <?php if (!empty($pembelian['catatan'])): ?>
            <div class="modern-card mt-4">
                <h5 class="mb-3">
                    <i class="fas fa-sticky-note me-2 text-primary"></i>
                    Catatan
                </h5>
                <div class="bg-light p-3 rounded">
                    <?= nl2br(htmlspecialchars($pembelian['catatan'])) ?>
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
                    <button class="btn btn-success approve-btn" data-id="<?= $pembelianId ?>" data-nomor="<?= htmlspecialchars($pembelian['nomor_pr'] ?? '') ?>" data-nama="<?= htmlspecialchars($pembelian['nama_panggilan'] ?? $pembelian['nama_lengkap'] ?? '') ?>">
                        <i class="fas fa-check me-2"></i>Setujui PR
                    </button>
                    <button class="btn btn-danger reject-btn" data-id="<?= $pembelianId ?>" data-nomor="<?= htmlspecialchars($pembelian['nomor_pr'] ?? '') ?>" data-nama="<?= htmlspecialchars($pembelian['nama_panggilan'] ?? $pembelian['nama_lengkap'] ?? '') ?>">
                        <i class="fas fa-times me-2"></i>Tolak PR
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
                    Tolak Purchase Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejectId">
                    <div class="mb-3">
                        <label class="form-label">Nomor PR</label>
                        <p class="fw-bold" id="rejectNomor"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Karyawan</label>
                        <p class="fw-bold" id="rejectNama"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasanPenolakan" class="form-control" rows="4" 
                                  placeholder="Isikan alasan penolakan purchase request..."></textarea>
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
            html: 'Setujui purchase request <strong>' + nomor + '</strong><br>atas nama <strong>' + nama + '</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/pembelian/approve') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/pembelian') ?>';
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
            text: 'Tolak purchase request ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/pembelian/reject') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/pembelian') ?>';
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
.table th {
    background: #f8f9fc;
}
</style>