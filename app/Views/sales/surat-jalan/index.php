<?php
$title = $title ?? 'Daftar Surat Jalan';
$active = $active ?? 'surat_jalan';

// Helper functions
function formatDate($dateString) {
    if (empty($dateString) || $dateString == '0000-00-00') return '-';
    return date('d/m/Y', strtotime($dateString));
}

function formatDateTime($dateString) {
    if (empty($dateString) || $dateString == '0000-00-00 00:00:00') return '-';
    return date('d/m/Y H:i', strtotime($dateString));
}

// Status config
$statusColors = [
    'draft' => 'secondary',
    'diproses' => 'warning',
    'dikirim' => 'info',
    'diterima' => 'success',
    'ditolak' => 'danger',
    'dibatalkan' => 'dark'
];

$statusText = [
    'draft' => 'Draft',
    'diproses' => 'Diproses',
    'dikirim' => 'Dikirim',
    'diterima' => 'Diterima',
    'ditolak' => 'Ditolak',
    'dibatalkan' => 'Dibatalkan'
];

$statusTerimaColors = [
    'pending' => 'warning',
    'diterima' => 'success',
    'ditolak' => 'danger'
];

$statusTerimaText = [
    'pending' => 'Menunggu',
    'diterima' => 'Diterima',
    'ditolak' => 'Ditolak'
];
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-truck me-3"></i>
                    <?= $title ?>
                </h1>
                <p class="lead text-muted">
                    <?= $subtitle ?? 'Kelola surat jalan pengiriman' ?>
                    <small class="d-block mt-1">
                        <i class="fas fa-user me-1"></i>Role: <?= $userRole ?? 'sales' ?> | 
                        <i class="fas fa-file-alt me-1"></i>Total: <?= $totalCount ?? 0 ?> surat jalan
                    </small>
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-5 border-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Diproses
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $statusCount['diproses'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-5 border-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Dikirim
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $statusCount['dikirim'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck-loading fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-5 border-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Diterima
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $statusCount['diterima'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-5 border-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                Dibatalkan
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $statusCount['dibatalkan'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-5 border-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-secondary text-uppercase mb-1">
                                Draft
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $statusCount['draft'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-5 border-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $totalCount ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Deliveries -->
    <?php if (!empty($todayDeliveries)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day me-2"></i>
                        Pengiriman Hari Ini (<?= date('d/m/Y') ?>)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Surat Jalan</th>
                                    <th>Project</th>
                                    <th>Client</th>
                                    <th>Penerima (UP)</th>
                                    <th>Status</th>
                                    <th>Status Terima</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($todayDeliveries as $delivery): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($delivery['nomor_surat_jalan']) ?></strong>
                                        <?php if (!empty($delivery['nomor_invoice'])): ?>
                                        <br>
                                        <small class="text-muted">
                                            Invoice: <?= htmlspecialchars($delivery['nomor_invoice']) ?>
                                        </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($delivery['nama_project'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($delivery['nama_perusahaan'] ?? '-') ?></td>
                                    <td>
                                        <?php 
                                        $penerimaUp = $delivery['penerima_up'] ?? $delivery['penerima_nama'] ?? '-';
                                        echo htmlspecialchars($penerimaUp);
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $statusColors[$delivery['status']] ?? 'secondary' ?>">
                                            <?= $statusText[$delivery['status']] ?? $delivery['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (isset($delivery['status_terima'])): ?>
                                        <span class="badge bg-<?= $statusTerimaColors[$delivery['status_terima']] ?? 'secondary' ?>">
                                            <?= $statusTerimaText[$delivery['status_terima']] ?? $delivery['status_terima'] ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('sales/surat-jalan/detail/' . $delivery['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Daftar Surat Jalan
                    </h5>
                    <div>
                        <a href="<?= base_url('sales/surat-jalan/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Buat Surat Jalan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Cari surat jalan...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="draft">Draft</option>
                                <option value="diproses">Diproses</option>
                                <option value="dikirim">Dikirim</option>
                                <option value="diterima">Diterima</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-secondary w-100" id="resetFilter">
                                <i class="fas fa-redo me-2"></i> Reset Filter
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="suratJalanTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">No. Surat Jalan</th>
                                    <th width="20%">Project & Client</th>
                                    <th width="12%">Tanggal Kirim</th>
                                    <th width="15%">Penerima</th>
                                    <th width="10%">Status</th>
                                    <th width="13%">Pengiriman</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($suratJalanList)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">Belum ada surat jalan</p>
                                            <a href="<?= base_url('sales/surat-jalan/create') ?>" class="btn btn-primary mt-3">
                                                <i class="fas fa-plus me-2"></i> Buat Surat Jalan Pertama
                                            </a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($suratJalanList as $sj): ?>
                                    <tr class="surat-jalan-row" data-status="<?= $sj['status'] ?>">
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong class="text-primary"><?= htmlspecialchars($sj['nomor_surat_jalan']) ?></strong>
                                                <?php if (!empty($sj['nomor_invoice'])): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-file-invoice me-1"></i>
                                                    <?= htmlspecialchars($sj['nomor_invoice']) ?>
                                                </small>
                                                <?php endif; ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?= formatDate($sj['tanggal_kirim']) ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong><?= htmlspecialchars($sj['nama_project'] ?? '-') ?></strong>
                                                <small class="text-muted">
                                                    <i class="fas fa-building me-1"></i>
                                                    <?= htmlspecialchars($sj['nama_perusahaan'] ?? '-') ?>
                                                </small>
                                                <?php if (!empty($sj['sales_nama'])): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-user-tie me-1"></i>
                                                    Sales: <?= htmlspecialchars($sj['sales_nama']) ?>
                                                </small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong><?= formatDate($sj['tanggal_kirim']) ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <?php 
                                                $penerimaPerusahaan = $sj['penerima_perusahaan'] ?? $sj['nama_perusahaan'] ?? '-';
                                                $penerimaUp = $sj['penerima_up'] ?? $sj['penerima_nama'] ?? '-';
                                                ?>
                                                <strong><?= htmlspecialchars($penerimaPerusahaan) ?></strong>
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    <?= htmlspecialchars($penerimaUp) ?>
                                                </small>
                                                <?php if (!empty($sj['penerima_telepon'])): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>
                                                    <?= htmlspecialchars($sj['penerima_telepon']) ?>
                                                </small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $statusColors[$sj['status']] ?? 'secondary' ?>">
                                                <?= $statusText[$sj['status']] ?? $sj['status'] ?>
                                            </span>
                                            <?php if ($sj['status'] == 'dikirim'): ?>
                                            <br>
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-clock"></i> Dalam perjalanan
                                            </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <?php if (!empty($sj['sopir'])): ?>
                                                <span class="fw-bold"><?= htmlspecialchars($sj['sopir']) ?></span>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($sj['no_kendaraan'])): ?>
                                                <span class="badge bg-dark mt-1">
                                                    <i class="fas fa-truck me-1"></i>
                                                    <?= htmlspecialchars($sj['no_kendaraan']) ?>
                                                </span>
                                                <?php else: ?>
                                                <small class="text-muted">Kendaraan: -</small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('sales/surat-jalan/detail/' . $sj['id']) ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if (in_array($sj['status'], ['draft', 'diproses', 'dikirim'])): ?>
                                                <a href="<?= base_url('sales/surat-jalan/edit/' . $sj['id']) ?>" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php endif; ?>
<!-- Ganti tombol cetak dengan: -->
<a href="<?= base_url('sales/surat-jalan/cetak-pdf/' . $sj['id']) ?>" 
   target="_blank"
   class="btn btn-sm btn-outline-danger" title="PDF">
    <i class="fas fa-file-pdf"></i>
</a>
<a href="<?= base_url('sales/surat-jalan/cetak/' . $sj['id']) ?>" 
   target="_blank"
   class="btn btn-sm btn-outline-success" title="Print">
    <i class="fas fa-print"></i>
</a>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                            type="button" 
                                                            data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <?php if (in_array($sj['status'], ['draft', 'diproses'])): ?>
                                                        <li>
                                                            <a class="dropdown-item text-danger" 
                                                               href="<?= base_url('sales/surat-jalan/delete/' . $sj['id']) ?>" 
                                                               onclick="return confirm('Yakin ingin menghapus surat jalan ini?')">
                                                                <i class="fas fa-trash me-2"></i> Hapus
                                                            </a>
                                                        </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Menampilkan <?= count($suratJalanList) ?> surat jalan
                        </div>
                        <div>
                            <a href="<?= base_url('sales/surat-jalan/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> Surat Jalan Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
.surat-jalan-row:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.card {
    border-radius: 10px;
    overflow: hidden;
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

.dropdown-menu {
    min-width: 180px;
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const resetFilter = document.getElementById('resetFilter');
    const rows = document.querySelectorAll('.surat-jalan-row');
    
    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const rowStatus = row.getAttribute('data-status');
            
            let showRow = true;
            
            // Search filter
            if (searchTerm && !rowText.includes(searchTerm)) {
                showRow = false;
            }
            
            // Status filter
            if (statusValue && rowStatus !== statusValue) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
            if (showRow) visibleCount++;
        });
        
        // Update counter
        updateCounter(visibleCount);
    }
    
    function updateCounter(count) {
        const counterElement = document.querySelector('.text-muted');
        if (counterElement && counterElement.textContent.includes('Menampilkan')) {
            counterElement.textContent = `Menampilkan ${count} surat jalan`;
        }
    }
    
    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', filterRows);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterRows);
    }
    
    if (resetFilter) {
        resetFilter.addEventListener('click', function() {
            searchInput.value = '';
            statusFilter.value = '';
            filterRows();
        });
    }
    
    // Row click - go to detail
    rows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on buttons or dropdown
            if (!e.target.closest('.btn-group') && !e.target.closest('a') && !e.target.closest('.dropdown')) {
                const detailLink = row.querySelector('a[href*="detail/"]');
                if (detailLink) {
                    window.location.href = detailLink.href;
                }
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Initialize filters
    filterRows();
});
</script>