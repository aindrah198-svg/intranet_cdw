<?php
$title = 'Daftar Absensi Karyawan';
$active = 'absensi';
?>

<style>
    /* Custom CSS for attendance index */
    .filter-card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .table-absensi {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
    }
    
    .table-absensi th {
        background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
        color: white;
        border: none;
        padding: 15px;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .table-absensi td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .table-absensi tr:hover {
        background-color: rgba(78, 115, 223, 0.03);
    }
    
    .badge-shift {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-shift-pagi { background: linear-gradient(135deg, #ffc107, #e0a800); color: #000; }
    .badge-shift-siang { background: linear-gradient(135deg, #28a745, #1e7e34); color: white; }
    .badge-shift-sore { background: linear-gradient(135deg, #17a2b8, #117a8b); color: white; }
    .badge-shift-malam { background: linear-gradient(135deg, #6f42c1, #5a32a3); color: white; }
    
    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        min-width: 70px;
        text-align: center;
    }
    
    .badge-status-hadir { background: linear-gradient(135deg, #d1f2eb, #a3e4d7); color: #155724; }
    .badge-status-izin { background: linear-gradient(135deg, #cce5ff, #99caff); color: #004085; }
    .badge-status-sakit { background: linear-gradient(135deg, #fff3cd, #ffeaa7); color: #856404; }
    .badge-status-cuti { background: linear-gradient(135deg, #e7f3ff, #d1e7ff); color: #0d6efd; }
    .badge-status-alpha { background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24; }
    
    .export-btn-group {
        display: flex;
        gap: 5px;
    }
    
    .export-btn {
        padding: 8px 15px;
        font-size: 0.875rem;
        border-radius: 5px;
        transition: all 0.3s;
    }
    
    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .filter-form .form-control,
    .filter-form .form-select {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s;
    }
    
    .filter-form .form-control:focus,
    .filter-form .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
    
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin: 2px;
    }
    
    .summary-card {
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    
    .summary-card .count {
        font-size: 2rem;
        font-weight: bold;
        color: #4e73df;
        line-height: 1;
    }
    
    .summary-card .label {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .quick-stats {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }
    
    .quick-stat {
        flex: 1;
        min-width: 120px;
        padding: 10px;
        border-radius: 8px;
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .export-btn-group {
            flex-direction: column;
            width: 100%;
        }
        
        .export-btn {
            width: 100%;
            margin-bottom: 5px;
        }
        
        .quick-stat {
            min-width: calc(50% - 5px);
        }
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-list-alt me-2"></i>Daftar Absensi Karyawan
            </h1>
            <p class="text-muted mb-0">Manajemen dan monitoring absensi seluruh karyawan</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/absensi/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Manual
            </a>
            <a href="<?= base_url('admin/absensi/my-attendance') ?>" class="btn btn-outline-primary">
                <i class="fas fa-user me-1"></i> Absensi Saya
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="filter-card p-3 bg-white">
                <h5 class="mb-3 text-primary"><i class="fas fa-chart-pie me-2"></i>Ringkasan Data</h5>
                <div class="quick-stats">
                    <div class="quick-stat">
                        <div class="count"><?= $totalAbsensi ?? 0 ?></div>
                        <div class="label">Total Absensi</div>
                    </div>
                    <div class="quick-stat">
                        <div class="count"><?= $totalKaryawan ?? 0 ?></div>
                        <div class="label">Karyawan</div>
                    </div>
                    <div class="quick-stat">
                        <div class="count"><?= $totalHadir ?? 0 ?></div>
                        <div class="label">Hadir</div>
                    </div>
                    <div class="quick-stat">
                        <div class="count"><?= $totalTerlambat ?? 0 ?></div>
                        <div class="label">Terlambat</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="filter-card p-4 bg-white">
                <h5 class="mb-3 text-primary"><i class="fas fa-filter me-2"></i>Filter Data</h5>
                <form method="get" class="filter-form">
                    <div class="row g-3">
                        <!-- Date Range -->
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?= $startDate ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?= $endDate ?? '' ?>">
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="Hadir" <?= ($statusFilter ?? '') == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="Izin" <?= ($statusFilter ?? '') == 'Izin' ? 'selected' : '' ?>>Izin</option>
                                <option value="Sakit" <?= ($statusFilter ?? '') == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                                <option value="Cuti" <?= ($statusFilter ?? '') == 'Cuti' ? 'selected' : '' ?>>Cuti</option>
                                <option value="Alpha" <?= ($statusFilter ?? '') == 'Alpha' ? 'selected' : '' ?>>Alpha</option>
                            </select>
                        </div>
                        
                        <!-- Employee Filter -->
                        <div class="col-md-2">
                            <label for="karyawan_id" class="form-label">Karyawan</label>
                            <select class="form-select" id="karyawan_id" name="karyawan_id">
                                <option value="">Semua Karyawan</option>
                                <?php if (!empty($karyawanList)): ?>
                                    <?php foreach ($karyawanList as $karyawan): ?>
                                        <option value="<?= $karyawan['id'] ?>" 
                                            <?= ($karyawanIdFilter ?? '') == $karyawan['id'] ? 'selected' : '' ?>>
                                            <?= esc($karyawan['nama_lengkap']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <!-- Search -->
                        <div class="col-md-2">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Nama/NIK..." value="<?= $searchQuery ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Terapkan Filter
                                    </button>
                                    <a href="<?= base_url('admin/absensi') ?>" class="btn btn-secondary ms-2">
                                        <i class="fas fa-redo me-1"></i> Reset
                                    </a>
                                </div>
                                
                                <div class="export-btn-group">
                                    <button type="button" class="btn btn-success export-btn" onclick="exportExcel()">
                                        <i class="fas fa-file-excel me-1"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger export-btn" onclick="exportPDF()">
                                        <i class="fas fa-file-pdf me-1"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="filter-card p-0 bg-white">
                <div class="table-responsive">
                    <table class="table table-absensi">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Karyawan</th>
                                <th>NIK</th>
                                <th>Shift</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Jam Kerja</th>
                                <th>Status</th>
                                <th>Terlambat</th>
                                <th>Lokasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($absensiData)): ?>
                                <?php $no = (($currentPage - 1) * $perPage) + 1; ?>
                                <?php foreach ($absensiData as $absensi): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($absensi['tanggal'])) ?><br>
                                            <small class="text-muted"><?= date('l', strtotime($absensi['tanggal'])) ?></small>
                                        </td>
                                        <td>
                                            <?= esc($absensi['nama_lengkap'] ?? '-') ?>
                                            <?php if ($absensi['departemen'] ?? ''): ?>
                                                <br><small class="text-muted"><?= esc($absensi['departemen']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($absensi['nik'] ?? '-') ?></td>
                                        <td>
                                            <?php if ($absensi['shift']): ?>
                                                <span class="badge-shift badge-shift-<?= $absensi['shift'] ?>">
                                                    <?= ucfirst($absensi['shift']) ?>
                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($absensi['waktu_masuk']): ?>
                                                <?= date('H:i', strtotime($absensi['waktu_masuk'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($absensi['waktu_pulang']): ?>
                                                <?= date('H:i', strtotime($absensi['waktu_pulang'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($absensi['jam_kerja']): ?>
                                                <span class="fw-bold <?= $absensi['jam_kerja'] >= 8 ? 'text-success' : 'text-warning' ?>">
                                                    <?= number_format($absensi['jam_kerja'], 1) ?> jam
                                                </span>
                                                <?php if ($absensi['jam_lembur'] > 0): ?>
                                                    <br><small class="text-danger">+<?= $absensi['jam_lembur'] ?>h lembur</small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($absensi['status']): ?>
                                                <span class="badge-status badge-status-<?= strtolower($absensi['status']) ?>">
                                                    <?= $absensi['status'] ?>
                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($absensi['terlambat'] && $absensi['terlambat'] > 0): ?>
                                                <span class="badge bg-danger">
                                                    <?php
                                                    $jam = floor($absensi['terlambat'] / 60);
                                                    $menit = $absensi['terlambat'] % 60;
                                                    if ($jam > 0) {
                                                        echo $jam . 'j ' . $menit . 'm';
                                                    } else {
                                                        echo $menit . 'm';
                                                    }
                                                    ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Tepat</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($absensi['lokasi_masuk'] && $absensi['lokasi_masuk'] != 'Lokasi tidak terdeteksi'): ?>
                                                <small class="text-muted" title="<?= esc($absensi['lokasi_masuk']) ?>">
                                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                    <?= esc(substr($absensi['lokasi_masuk'], 0, 20)) ?>...
                                                </small>
                                            <?php else: ?>
                                                <small class="text-muted">-</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <!-- View Button -->
                                                <a href="<?= base_url('admin/absensi/detail/' . $absensi['id']) ?>" 
                                                   class="btn btn-sm btn-info btn-action" 
                                                   title="Detail"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Edit Button -->
                                                <a href="<?= base_url('admin/absensi/edit/' . $absensi['id']) ?>" 
                                                   class="btn btn-sm btn-warning btn-action" 
                                                   title="Edit"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Delete Button -->
                                                <button onclick="confirmDelete(<?= $absensi['id'] ?>, '<?= esc($absensi['nama_lengkap']) ?>', '<?= date('d/m/Y', strtotime($absensi['tanggal'])) ?>')" 
                                                        class="btn btn-sm btn-danger btn-action" 
                                                        title="Hapus"
                                                        data-bs-toggle="tooltip">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                
                                                <!-- Checkout Button (if not checked out) -->
                                                <?php if ($absensi['waktu_masuk'] && !$absensi['waktu_pulang'] && $absensi['status'] == 'Hadir'): ?>
                                                    <button onclick="manualCheckout(<?= $absensi['id'] ?>, '<?= esc($absensi['nama_lengkap']) ?>')" 
                                                            class="btn btn-sm btn-success btn-action" 
                                                            title="Checkout Manual"
                                                            data-bs-toggle="tooltip">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3"></i><br>
                                            Tidak ada data absensi
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

  <!-- Pagination -->
<?php if (!empty($absensiData) && $totalAbsensi > $perPage): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <nav aria-label="Page navigation">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Menampilkan <?= (($currentPage - 1) * $perPage) + 1 ?> - 
                    <?= min($currentPage * $perPage, $totalAbsensi) ?> dari <?= $totalAbsensi ?> data
                </div>
                
                <ul class="pagination pagination-sm mb-0">
                    <!-- Previous Page -->
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">&laquo;</span>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Page Numbers -->
                    <?php 
                    // Show page numbers with ellipsis
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    
                    // Show first page if not in range
                    if ($startPage > 1): ?>
                        <li class="page-item <?= 1 == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>&page=1">1</a>
                        </li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Show last page if not in range -->
                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item <?= $totalPages == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>&page=<?= $totalPages ?>"><?= $totalPages ?></a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Next Page -->
                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">&raquo;</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</div>
<?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="icon-circle danger mb-3" style="width: 70px; height: 70px; margin: 0 auto; background: linear-gradient(135deg, #dc3545, #c82333);">
                        <i class="fas fa-trash fa-2x text-white"></i>
                    </div>
                    <h5 id="deleteMessage"></h5>
                    <p class="text-muted mt-2">Data yang dihapus tidak dapat dikembalikan.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Manual Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-sign-out-alt me-2"></i>Checkout Manual
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="checkoutMessage" class="mb-3"></div>
                <form id="checkoutForm">
                    <input type="hidden" id="checkoutAbsensiId">
                    <div class="mb-3">
                        <label for="checkoutTime" class="form-label">Waktu Pulang</label>
                        <input type="time" class="form-control" id="checkoutTime" required>
                    </div>
                    <div class="mb-3">
                        <label for="checkoutNote" class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" id="checkoutNote" rows="2" placeholder="Tambahkan catatan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitCheckout()">
                    <i class="fas fa-check me-1"></i> Simpan Checkout
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Tooltip initialization
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Confirm delete function
let deleteAbsensiId = null;

function confirmDelete(id, namaKaryawan, tanggal) {
    deleteAbsensiId = id;
    const message = `Hapus absensi ${namaKaryawan} pada ${tanggal}?`;
    document.getElementById('deleteMessage').textContent = message;
    $('#deleteModal').modal('show');
}

// Handle delete confirmation
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteAbsensiId) return;
    
    const btn = this;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...';
    btn.disabled = true;
    
    fetch(`<?= base_url('admin/absensi/delete/') ?>${deleteAbsensiId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            showToast(result.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(result.message || 'Gagal menghapus data', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat menghapus data', 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        $('#deleteModal').modal('hide');
    });
});

// Manual checkout function
function manualCheckout(id, namaKaryawan) {
    document.getElementById('checkoutAbsensiId').value = id;
    document.getElementById('checkoutMessage').innerHTML = `
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Checkout manual untuk <strong>${namaKaryawan}</strong>
        </div>
    `;
    
     // Set current time as default checkout time
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    document.getElementById('checkoutTime').value = `${hours}:${minutes}`;
    
    $('#checkoutModal').modal('show');
}

// Submit checkout
function submitCheckout() {
    const absensiId = document.getElementById('checkoutAbsensiId').value;
    const waktuPulang = document.getElementById('checkoutTime').value;
    const keterangan = document.getElementById('checkoutNote').value;
    
    if (!waktuPulang) {
        showToast('Waktu pulang harus diisi', 'error');
        return;
    }
    
    // Validate time format
    if (!/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(waktuPulang)) {
        showToast('Format waktu tidak valid. Gunakan format HH:MM', 'error');
        return;
    }
    
    const btn = document.querySelector('#checkoutModal .btn-success');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
    btn.disabled = true;
    
    fetch(`<?= base_url('admin/absensi/checkout-manual/') ?>${absensiId}`, {  // <-- PERUBAHAN DI SINI
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            waktu_pulang: waktuPulang,
            keterangan: keterangan
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            showToast(result.message, 'success');
            $('#checkoutModal').modal('hide');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(result.message || 'Gagal melakukan checkout', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat checkout', 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Export functions
function exportExcel() {
    const params = new URLSearchParams(window.location.search);
    window.open(`<?= base_url('admin/absensi/export/excel?') ?>${params.toString()}`, '_blank');
}

function exportPDF() {
    const params = new URLSearchParams(window.location.search);
    window.open(`<?= base_url('admin/absensi/export/pdf?') ?>${params.toString()}`, '_blank');
}

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '1060';
    toast.style.minWidth = '300px';
    toast.style.animation = 'slideIn 0.3s ease-out';
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${type === 'success' ? '<i class="fas fa-check-circle me-2"></i>' : 
                  type === 'error' ? '<i class="fas fa-exclamation-circle me-2"></i>' : 
                  '<i class="fas fa-info-circle me-2"></i>'}
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Auto-submit filter on certain changes
document.getElementById('status').addEventListener('change', function() {
    if (this.value) {
        this.form.submit();
    }
});

// Quick date range buttons
function setDateRange(days) {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days);
    
    document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
    document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
    document.querySelector('form').submit();
}

// Initialize date filters with default values if not set
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (!startDateInput.value) {
        const firstDay = new Date();
        firstDay.setDate(1);
        startDateInput.value = firstDay.toISOString().split('T')[0];
    }
    
    if (!endDateInput.value) {
        const today = new Date();
        endDateInput.value = today.toISOString().split('T')[0];
    }
});
</script>

