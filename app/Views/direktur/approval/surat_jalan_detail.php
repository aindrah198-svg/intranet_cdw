<?php
// app/Views/direktur/approval/surat_jalan_detail.php

$suratJalan = $suratJalan ?? [];
$items = $items ?? [];
$suratJalanId = $suratJalan['id'] ?? 0;
$isPending = ($suratJalan['status_direktur'] ?? '') === 'Menunggu' && ($suratJalan['status_hrd'] ?? '') === 'Disetujui HRD';

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

// Helper function untuk badge status pengiriman
if (!function_exists('getStatusPengirimanBadge')) {
    function getStatusPengirimanBadge($status) {
        $badges = [
            'diproses' => '<span class="badge bg-secondary">Diproses</span>',
            'dikirim' => '<span class="badge bg-primary">Dikirim</span>',
            'diterima' => '<span class="badge bg-success">Diterima</span>',
            'dibatalkan' => '<span class="badge bg-danger">Dibatalkan</span>'
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
                <i class="fas fa-truck me-2"></i>Detail Surat Jalan
            </h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/approval/surat-jalan') ?>" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </p>
        </div>
        <div>
            <a href="<?= base_url('direktur/approval/surat-jalan/print/' . $suratJalanId) ?>" 
               class="btn btn-modern-outline me-2" target="_blank">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
            <?php if ($isPending): ?>
            <button type="button" class="btn btn-success approve-btn" data-id="<?= $suratJalanId ?>" data-nomor="<?= htmlspecialchars($suratJalan['nomor_surat_jalan'] ?? '') ?>">
                <i class="fas fa-check me-2"></i>Setujui Surat Jalan
            </button>
            <button type="button" class="btn btn-danger reject-btn" data-id="<?= $suratJalanId ?>" data-nomor="<?= htmlspecialchars($suratJalan['nomor_surat_jalan'] ?? '') ?>">
                <i class="fas fa-times me-2"></i>Tolak Surat Jalan
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Informasi Surat Jalan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Informasi Surat Jalan
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Nomor Surat Jalan</label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($suratJalan['nomor_surat_jalan'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Tanggal Kirim</label>
                        <p class="mb-0"><?= formatDateIndo($suratJalan['tanggal_kirim'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Status Pengiriman</label>
                        <p class="mb-0"><?= getStatusPengirimanBadge($suratJalan['status'] ?? 'diproses') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Status Penerimaan</label>
                        <p class="mb-0">
                            <?php if (($suratJalan['status_terima'] ?? '') === 'diterima'): ?>
                                <span class="badge bg-success">Diterima</span>
                                <?php if (!empty($suratJalan['tanggal_terima'])): ?>
                                    <small class="d-block"><?= formatDateTime($suratJalan['tanggal_terima']) ?></small>
                                <?php endif; ?>
                            <?php elseif (($suratJalan['status_terima'] ?? '') === 'ditolak'): ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informasi Project & Client -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-briefcase me-2 text-primary"></i>
                    Informasi Project & Client
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Project</label>
                        <p class="mb-0">
                            <strong><?= htmlspecialchars($suratJalan['nama_project'] ?? '-') ?></strong><br>
                            <small class="text-muted">Kode: <?= htmlspecialchars($suratJalan['kode_project'] ?? '-') ?></small>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Client</label>
                        <p class="mb-0">
                            <strong><?= htmlspecialchars($suratJalan['client_nama'] ?? '-') ?></strong><br>
                            <small class="text-muted"><?= nl2br(htmlspecialchars($suratJalan['client_alamat'] ?? '-')) ?></small>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">No. Invoice</label>
                        <p class="mb-0"><?= htmlspecialchars($suratJalan['nomor_invoice'] ?? '-') ?></p>
                    </div>
                    <?php if (!empty($suratJalan['lokasi_proyek'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Lokasi Proyek</label>
                        <p class="mb-0"><?= htmlspecialchars($suratJalan['lokasi_proyek']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informasi Pengiriman -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-truck-moving me-2 text-primary"></i>
                    Informasi Pengiriman
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Penerima</label>
                        <p class="mb-0">
                            <strong><?= htmlspecialchars($suratJalan['penerima_nama'] ?? '-') ?></strong><br>
                            <small><?= htmlspecialchars($suratJalan['penerima_perusahaan'] ?? '') ?></small>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">UP / Contact Person</label>
                        <p class="mb-0"><?= htmlspecialchars($suratJalan['penerima_up'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Telepon Penerima</label>
                        <p class="mb-0"><?= htmlspecialchars($suratJalan['penerima_telepon'] ?? '-') ?></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1 d-block">Alamat Pengiriman</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($suratJalan['alamat_pengiriman'] ?? '-')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Kurir -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-user me-2 text-primary"></i>
                    Informasi Kurir
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Nama Sopir</label>
                        <p class="mb-0"><?= htmlspecialchars($suratJalan['sopir'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">No. Kendaraan</label>
                        <p class="mb-0"><?= htmlspecialchars($suratJalan['no_kendaraan'] ?? '-') ?></p>
                    </div>
                    <?php if (!empty($suratJalan['disiapkan_oleh'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Disiapkan Oleh</label>
                        <p class="mb-0"><?= htmlspecialchars($suratJalan['disiapkan_oleh']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($suratJalan['dikirim_oleh'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Dikirim Oleh</label>
                        <p class="mb-0"><?= htmlspecialchars($suratJalan['dikirim_oleh']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($suratJalan['diterima_oleh'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1 d-block">Diterima Oleh</label>
                        <p class="mb-0"><?= htmlspecialchars($suratJalan['diterima_oleh']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Daftar Barang -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-boxes me-2 text-primary"></i>
                    Daftar Barang
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Barang</th>
                                <th width="100">Qty</th>
                                <th width="80">Satuan</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-box-open fa-2x text-muted mb-2 d-block"></i>
                                    Tidak ada item
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; ?>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                    <td class="text-center"><?= number_format($item['qty'], 2) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($item['satuan'] ?? 'pcs') ?></td>
                                    <td><?= htmlspecialchars($item['keterangan'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (!empty($suratJalan['catatan_barang'])): ?>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">Catatan Barang: <?= nl2br(htmlspecialchars($suratJalan['catatan_barang'])) ?></small>
                </div>
                <?php endif; ?>
            </div>

            <!-- Keterangan -->
            <?php if (!empty($suratJalan['keterangan'])): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-sticky-note me-2 text-primary"></i>
                    Keterangan
                </h5>
                <div class="bg-light p-3 rounded">
                    <?= nl2br(htmlspecialchars($suratJalan['keterangan'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Pengajuan -->
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Status Persetujuan
                </h5>
                <div class="row">
                    <div class="col-12 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-users fa-2x mb-2 <?= ($suratJalan['status_hrd'] ?? '') === 'Disetujui HRD' ? 'text-success' : (($suratJalan['status_hrd'] ?? '') === 'Ditolak HRD' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Approval HRD</h6>
                            <?php if (($suratJalan['status_hrd'] ?? '') === 'Disetujui HRD'): ?>
                                <span class="badge bg-success">Disetujui</span>
                                <?php if (!empty($suratJalan['hrd_nama'])): ?>
                                    <small class="d-block mt-1">Oleh: <?= htmlspecialchars($suratJalan['hrd_nama']) ?></small>
                                <?php endif; ?>
                            <?php elseif (($suratJalan['status_hrd'] ?? '') === 'Ditolak HRD'): ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3 text-center">
                        <div class="border rounded p-3">
                            <i class="fas fa-crown fa-2x mb-2 <?= ($suratJalan['status_direktur'] ?? '') === 'Disetujui' ? 'text-success' : (($suratJalan['status_direktur'] ?? '') === 'Ditolak' ? 'text-danger' : 'text-warning') ?>"></i>
                            <h6 class="mb-1">Approval Direktur</h6>
                            <?= getDirekturStatusBadge($suratJalan['status_direktur'] ?? 'Menunggu') ?>
                            <?php if (!empty($suratJalan['direktur_nama']) && ($suratJalan['status_direktur'] ?? '') !== 'Menunggu'): ?>
                                <small class="d-block mt-1">Oleh: <?= htmlspecialchars($suratJalan['direktur_nama']) ?></small>
                                <small class="d-block"><?= formatDateTime($suratJalan['disetujui_direktur_at'] ?? '') ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Perusahaan Pengirim -->
            <?php if (!empty($suratJalan['perusahaan_pengirim_nama'])): ?>
            <div class="modern-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-building me-2 text-primary"></i>
                    Perusahaan Pengirim
                </h5>
                <div class="mb-2">
                    <strong><?= htmlspecialchars($suratJalan['perusahaan_pengirim_nama']) ?></strong>
                </div>
                <?php if (!empty($suratJalan['perusahaan_pengirim_alamat'])): ?>
                <div class="mb-2 text-muted small">
                    <?= nl2br(htmlspecialchars($suratJalan['perusahaan_pengirim_alamat'])) ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($suratJalan['perusahaan_pengirim_website'])): ?>
                <div class="small">
                    <i class="fas fa-globe me-1"></i> <?= htmlspecialchars($suratJalan['perusahaan_pengirim_website']) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Alasan Penolakan -->
            <?php if (!empty($suratJalan['alasan_penolakan_direktur'])): ?>
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-danger"></i>
                    Alasan Penolakan
                </h5>
                <div class="bg-danger bg-opacity-10 p-3 rounded">
                    <i class="fas fa-ban me-1 text-danger"></i>
                    <?= nl2br(htmlspecialchars($suratJalan['alasan_penolakan_direktur'])) ?>
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
                    <button class="btn btn-success approve-btn" data-id="<?= $suratJalanId ?>" data-nomor="<?= htmlspecialchars($suratJalan['nomor_surat_jalan'] ?? '') ?>">
                        <i class="fas fa-check me-2"></i>Setujui Surat Jalan
                    </button>
                    <button class="btn btn-danger reject-btn" data-id="<?= $suratJalanId ?>" data-nomor="<?= htmlspecialchars($suratJalan['nomor_surat_jalan'] ?? '') ?>">
                        <i class="fas fa-times me-2"></i>Tolak Surat Jalan
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
                    Tolak Surat Jalan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejectId">
                    <div class="mb-3">
                        <label class="form-label">Nomor Surat Jalan</label>
                        <p class="fw-bold" id="rejectNomor"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasanPenolakan" class="form-control" rows="4" 
                                  placeholder="Isikan alasan penolakan surat jalan..."></textarea>
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
            html: 'Setujui surat jalan <strong>' + nomor + '</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/surat-jalan/approve') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/surat-jalan') ?>';
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
            text: 'Tolak surat jalan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/surat-jalan/reject') ?>/' + id,
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
                                window.location.href = '<?= base_url('direktur/approval/surat-jalan') ?>';
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