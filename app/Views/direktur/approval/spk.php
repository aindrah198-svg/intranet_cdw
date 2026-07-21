<?php
// app/Views/direktur/approval/spk.php

$spkData = $spkData ?? [];
$statusFilter = $statusFilter ?? '';
$searchQuery = $searchQuery ?? '';
$startDate = $startDate ?? date('Y-m-d', strtotime('-3 months'));
$endDate = $endDate ?? date('Y-m-d');
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 15;
$totalData = $totalData ?? 0;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? base_url('direktur/approval/spk');
$queryParams = $queryParams ?? [];
$stats = $stats ?? ['total_spk' => 0, 'total_draft' => 0, 'total_disetujui' => 0, 'total_ditolak' => 0, 'total_on_progress' => 0, 'total_selesai' => 0, 'total_nilai_kontrak' => 0];
$pendingCount = $pendingCount ?? 0;

// Helper function untuk format tanggal
if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) return '-';
        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : '-';
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
                <i class="fas fa-file-contract me-2"></i>Approval SPK
            </h4>
            <p class="text-muted mb-0">Persetujuan Surat Perintah Kerja oleh Direktur</p>
        </div>
        <div>
            <button class="btn btn-modern-outline me-2" id="batchApproveBtn" style="display: none;">
                <i class="fas fa-check-double me-2"></i>Approve Terpilih
            </button>
            <button class="btn btn-modern-outline me-2" onclick="window.location.href='<?= base_url('direktur/approval/spk/export-excel?' . http_build_query(array_filter($queryParams))) ?>'">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-primary text-center">
                <div class="mb-2">
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
                <h2 class="mb-0"><?= number_format($stats['total_draft'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Menunggu Persetujuan</p>
                <?php if (($stats['total_draft'] ?? 0) > 0): ?>
                    <small class="text-warning">Perlu tindakan</small>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-accent text-center">
                <div class="mb-2">
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <h2 class="mb-0"><?= number_format($stats['total_disetujui'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Telah Disetujui</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-danger text-center">
                <div class="mb-2">
                    <i class="fas fa-chart-line fa-2x text-info"></i>
                </div>
                <h2 class="mb-0"><?= number_format(($stats['total_on_progress'] ?? 0) + ($stats['total_selesai'] ?? 0)) ?></h2>
                <p class="text-muted mb-0">On Progress / Selesai</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-warning text-center">
                <div class="mb-2">
                    <i class="fas fa-money-bill-wave fa-2x text-primary"></i>
                </div>
                <h2 class="mb-0"><?= formatCurrency($stats['total_nilai_kontrak'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Total Nilai Kontrak</p>
                <small class="text-muted"><?= number_format($stats['total_spk'] ?? 0) ?> SPK</small>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="modern-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-filter me-2 text-primary"></i>
            Filter Data
        </h5>
        <form method="GET" action="<?= base_url('direktur/approval/spk') ?>" class="row g-3" id="filterForm">
            <div class="col-md-3">
                <label class="form-label">Status SPK</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Menunggu (Draft)</option>
                    <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                    <option value="progress" <?= $statusFilter === 'progress' ? 'selected' : '' ?>>On Progress</option>
                    <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="No. SPK / Client / Judul..." value="<?= htmlspecialchars($searchQuery) ?>">
            </div>
            <div class="col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="<?= base_url('direktur/approval/spk') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Data SPK -->
    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                Daftar Surat Perintah Kerja (SPK)
                <?php if ($pendingCount > 0): ?>
                    <span class="badge bg-danger ms-2"><?= $pendingCount ?> baru</span>
                <?php endif; ?>
            </h5>
            <div>
                <span class="text-muted">Total: <?= number_format($totalData) ?> data</span>
                <span class="text-muted ms-3">Halaman <?= $currentPage ?> dari <?= $totalPages ?></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th width="50">No</th>
                        <th>No. SPK</th>
                        <th>Tanggal</th>
                        <th>Client</th>
                        <th>Judul Pekerjaan</th>
                        <th>Nilai Kontrak</th>
                        <th>Penanggung Jawab</th>
                        <th>Status</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($spkData)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            Tidak ada data SPK
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $no = (($currentPage - 1) * $perPage) + 1; 
                        foreach ($spkData as $item): 
                            $isPending = ($item['status'] === 'draft');
                        ?>
                        <tr class="<?= $isPending ? 'table-warning' : '' ?>">
                            <td>
                                <?php if ($isPending): ?>
                                <input type="checkbox" name="selected_ids[]" value="<?= $item['id'] ?>" class="form-check-input row-checkbox">
                                <?php endif; ?>
                            </td>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($item['nomor_spk']) ?></strong>
                            </td>
                            <td><?= formatDate($item['created_at'] ?? '') ?></td>
                            <td>
                                <div>
                                    <strong><?= htmlspecialchars($item['nama_perusahaan']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($item['kode_client'] ?? '-') ?></small>
                                </div>
                            </td>
                            <td>
                                <span title="<?= htmlspecialchars($item['judul_pekerjaan'] ?? '') ?>">
                                    <?= htmlspecialchars(substr($item['judul_pekerjaan'] ?? '-', 0, 50)) ?>
                                    <?= strlen($item['judul_pekerjaan'] ?? '') > 50 ? '...' : '' ?>
                                </span>
                            </td>
                            <td class="fw-bold"><?= formatCurrency($item['nilai_kontrak'] ?? 0) ?></td>
                            <td>
                                <?php if (!empty($item['penanggung_jawab_nama'])): ?>
                                    <?= htmlspecialchars($item['penanggung_jawab_panggilan'] ?? $item['penanggung_jawab_nama']) ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= getSpkStatusBadge($item['status']) ?></td>
                            <td>
                                <a href="<?= base_url('direktur/approval/spk/detail/' . $item['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($isPending): ?>
                                <button type="button" class="btn btn-sm btn-success approve-btn" data-id="<?= $item['id'] ?>" data-nomor="<?= htmlspecialchars($item['nomor_spk']) ?>" title="Setujui">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger reject-btn" data-id="<?= $item['id'] ?>" data-nomor="<?= htmlspecialchars($item['nomor_spk']) ?>" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php endif; ?>
                                <a href="<?= base_url('direktur/approval/spk/print/' . $item['id']) ?>" 
                                   class="btn btn-sm btn-outline-secondary" target="_blank" title="Cetak">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination justify-content-center">
                <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
                <?php endif; ?>
                
                <?php 
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                for ($i = $startPage; $i <= $endPage; $i++): 
                ?>
                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
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
    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        toggleBatchButton();
    });
    
    $('.row-checkbox').on('change', function() {
        toggleBatchButton();
    });
    
    function toggleBatchButton() {
        var checkedCount = $('.row-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#batchApproveBtn').show();
        } else {
            $('#batchApproveBtn').hide();
        }
    }
    
    // Batch Approve
    $('#batchApproveBtn').on('click', function() {
        var selectedIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Tidak ada data yang dipilih'
            });
            return;
        }
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Anda akan menyetujui ' + selectedIds.length + ' SPK?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/spk/batch-approve') ?>',
                    type: 'POST',
                    data: { ids: selectedIds },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
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
                                window.location.reload();
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
                                window.location.reload();
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
    
    // Auto submit filter on date change
    $('input[name="start_date"], input[name="end_date"]').on('change', function() {
        $('#filterForm').submit();
    });
});
</script>

<style>
.avatar-sm {
    width: 32px;
    min-width: 32px;
}
.modern-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.modern-card-primary {
    border-left: 4px solid #4e73df;
}
.modern-card-accent {
    border-left: 4px solid #1cc88a;
}
.modern-card-warning {
    border-left: 4px solid #f6c23e;
}
.modern-card-danger {
    border-left: 4px solid #e74a3b;
}
.text-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.table th {
    background: #f8f9fc;
    font-weight: 600;
    border-bottom: 2px solid #e3e6f0;
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
.table-warning {
    background-color: #fff3cd !important;
}
</style>