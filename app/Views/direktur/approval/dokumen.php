<?php
// app/Views/direktur/approval/dokumen.php

$dokumenData = $dokumenData ?? [];
$jenisDokumenList = $jenisDokumenList ?? [];
$statusFilter = $statusFilter ?? '';
$jenisFilter = $jenisFilter ?? '';
$searchQuery = $searchQuery ?? '';
$startDate = $startDate ?? date('Y-m-d', strtotime('-3 months'));
$endDate = $endDate ?? date('Y-m-d');
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 15;
$totalData = $totalData ?? 0;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? base_url('direktur/approval/dokumen');
$queryParams = $queryParams ?? [];
$stats = $stats ?? ['total_dokumen' => 0, 'total_menunggu' => 0, 'total_disetujui' => 0, 'total_ditolak' => 0, 'total_diproses' => 0, 'total_selesai' => 0];
$pendingCount = $pendingCount ?? 0;

// Helper function untuk format tanggal
if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) return '-';
        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : '-';
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
                <i class="fas fa-file-alt me-2"></i>Approval Dokumen
            </h4>
            <p class="text-muted mb-0">Persetujuan Pengajuan Dokumen Karyawan oleh Direktur</p>
        </div>
        <div>
            <button class="btn btn-modern-outline me-2" id="batchApproveBtn" style="display: none;">
                <i class="fas fa-check-double me-2"></i>Approve Terpilih
            </button>
            <button class="btn btn-modern-outline me-2" onclick="window.location.href='<?= base_url('direktur/approval/dokumen/export-excel?' . http_build_query(array_filter($queryParams))) ?>'">
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
                <h2 class="mb-0"><?= number_format($stats['total_menunggu'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Menunggu Persetujuan</p>
                <?php if (($stats['total_menunggu'] ?? 0) > 0): ?>
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
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                </div>
                <h2 class="mb-0"><?= number_format($stats['total_ditolak'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Ditolak</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-warning text-center">
                <div class="mb-2">
                    <i class="fas fa-chart-line fa-2x text-primary"></i>
                </div>
                <h2 class="mb-0"><?= number_format($stats['total_diproses'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Sedang Diproses HRD</p>
                <small class="text-muted"><?= number_format($stats['total_selesai'] ?? 0) ?> selesai</small>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="modern-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-filter me-2 text-primary"></i>
            Filter Data
        </h5>
        <form method="GET" action="<?= base_url('direktur/approval/dokumen') ?>" class="row g-3" id="filterForm">
            <div class="col-md-2">
                <label class="form-label">Status Approval</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                    <option value="processed" <?= $statusFilter === 'processed' ? 'selected' : '' ?>>Diproses HRD</option>
                    <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis Dokumen</label>
                <select name="jenis" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    <?php foreach ($jenisDokumenList as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $jenisFilter === $key ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="No. Form / Nama / NIK..." value="<?= htmlspecialchars($searchQuery) ?>">
            </div>
            <div class="col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="<?= base_url('direktur/approval/dokumen') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Data Dokumen -->
    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                Daftar Pengajuan Dokumen
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
                        <th>No. Form</th>
                        <th>Tgl Pengajuan</th>
                        <th>Karyawan</th>
                        <th>Jenis Dokumen</th>
                        <th>Keperluan</th>
                        <th>Status HRD</th>
                        <th>Status Direktur</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dokumenData)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            Tidak ada data pengajuan dokumen
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $no = (($currentPage - 1) * $perPage) + 1; 
                        foreach ($dokumenData as $item): 
                            $isPending = ($item['status_direktur'] === 'Menunggu' && $item['status_hrd'] === 'Diproses');
                        ?>
                        <tr class="<?= $isPending ? 'table-warning' : '' ?>">
                            <td>
                                <?php if ($isPending): ?>
                                <input type="checkbox" name="selected_ids[]" value="<?= $item['id'] ?>" class="form-check-input row-checkbox">
                                <?php endif; ?>
                            </td>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($item['nomor_form']) ?></strong>
                            </td>
                            <td><?= formatDate($item['tanggal_pengajuan'] ?? '') ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 32px; height: 32px;">
                                            <?= strtoupper(substr($item['nama_panggilan'] ?? $item['nama_lengkap'] ?? '?', 0, 1)) ?>
                                        </div>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($item['nama_panggilan'] ?? $item['nama_lengkap']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($item['nik'] ?? '-') ?> | <?= htmlspecialchars($item['jabatan'] ?? '-') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= getJenisDokumenBadge($item['jenis_dokumen']) ?></td>
                            <td>
                                <span title="<?= htmlspecialchars($item['keperluan'] ?? '') ?>">
                                    <?= htmlspecialchars(substr($item['keperluan'] ?? '-', 0, 40)) ?>
                                    <?= strlen($item['keperluan'] ?? '') > 40 ? '...' : '' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($item['status_hrd'] === 'Diproses'): ?>
                                    <span class="badge bg-primary">Diproses</span>
                                <?php elseif ($item['status_hrd'] === 'Selesai'): ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php elseif ($item['status_hrd'] === 'Ditolak'): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= $item['status_hrd'] ?? '-' ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= getDirekturStatusBadge($item['status_direktur'] ?? 'Menunggu') ?></td>
                            <td>
                                <a href="<?= base_url('direktur/approval/dokumen/detail/' . $item['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($isPending): ?>
                                <button type="button" class="btn btn-sm btn-success approve-btn" data-id="<?= $item['id'] ?>" data-nomor="<?= htmlspecialchars($item['nomor_form']) ?>" data-nama="<?= htmlspecialchars($item['nama_panggilan'] ?? $item['nama_lengkap']) ?>" title="Setujui">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger reject-btn" data-id="<?= $item['id'] ?>" data-nomor="<?= htmlspecialchars($item['nomor_form']) ?>" data-nama="<?= htmlspecialchars($item['nama_panggilan'] ?? $item['nama_lengkap']) ?>" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php endif; ?>
                                <a href="<?= base_url('direktur/approval/dokumen/print/' . $item['id']) ?>" 
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
            text: 'Anda akan menyetujui ' + selectedIds.length + ' pengajuan dokumen?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('direktur/approval/dokumen/batch-approve') ?>',
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