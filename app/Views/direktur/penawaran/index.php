<?php
// app/Views/direktur/penawaran/index.php
$title = 'Manajemen Penawaran';
$subtitle = 'Pengajuan Penawaran';
$user = $user ?? session()->get();
$active = 'penawaran';
?>

<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<style>
    /* Custom CSS for penawaran management */
    .penawaran-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    
    .penawaran-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .card-header-penawaran {
        background: linear-gradient(135deg, var(--cdw-primary) 0%, var(--cdw-secondary) 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px;
    }
    
    .card-title-penawaran {
        font-weight: 600;
        margin: 0;
        font-size: 1.2rem;
    }
    
    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        min-width: 100px;
        text-align: center;
    }
    
    .status-draft { background: linear-gradient(135deg, #6c757d, #5a6268); color: white; }
    .status-sent { background: linear-gradient(135deg, #17a2b8, #138496); color: white; }
    .status-revisi { background: linear-gradient(135deg, #ffc107, #e0a800); color: #000; }
    .status-diterima { background: linear-gradient(135deg, #28a745, #1e7e34); color: white; }
    .status-ditolak { background: linear-gradient(135deg, #dc3545, #c82333); color: white; }
    .status-kadaluarsa { background: linear-gradient(135deg, #20c997, #199d76); color: white; }
    
    .table-penawaran {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
    }
    
    .table-penawaran th {
        background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
        color: white;
        border: none;
        padding: 15px;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .table-penawaran td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .table-penawaran tr:hover {
        background-color: rgba(78, 115, 223, 0.03);
    }
    
    .action-btn-group {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 0.875rem;
    }
    
    .summary-box {
        text-align: center;
        padding: 20px;
        border-radius: 10px;
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        height: 100%;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .summary-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .summary-box-1 { border-left-color: var(--cdw-primary); }
    .summary-box-2 { border-left-color: var(--cdw-secondary); }
    .summary-box-3 { border-left-color: var(--cdw-accent); }
    .summary-box-4 { border-left-color: var(--cdw-warning); }
    .summary-box-5 { border-left-color: var(--cdw-danger); }
    .summary-box-6 { border-left-color: var(--cdw-info); }
    
    .summary-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2a4b8c;
        line-height: 1;
        margin-bottom: 10px;
    }
    
    .summary-label {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .client-logo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--cdw-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        margin-right: 10px;
        flex-shrink: 0;
    }
    
    .value-indicator {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .value-high { background: #d4edda; color: #155724; }
    .value-medium { background: #fff3cd; color: #856404; }
    .value-low { background: #f8d7da; color: #721c24; }
    
    .quick-filter {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .export-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    @media (max-width: 768px) {
        .action-btn-group {
            flex-direction: column;
        }
        
        .action-btn {
            width: 100%;
        }
        
        .export-options {
            flex-direction: column;
        }
        
        .export-options .btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-1">
                <i class="fas fa-handshake me-2"></i>Manajemen Penawaran
            </h1>
            <p class="page-subtitle">Kelola dan approve penawaran / quotation dari tim sales</p>
        </div>
        <div class="export-options">
            <!-- TAMBAHKAN TOMBOL CREATE -->
            <a href="<?= base_url('direktur/penawaran/create') ?>" class="btn btn-modern-primary">
                <i class="fas fa-plus me-1"></i> Buat Penawaran
            </a>
            <button class="btn btn-modern-outline" onclick="window.location.href='<?= base_url('direktur/penawaran') ?>'">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </a>
            <a href="<?= base_url('direktur/penawaran/export/excel') ?>" class="btn btn-modern-outline">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="summary-box summary-box-1">
                <div class="summary-number"><?= $stats['total'] ?? 0 ?></div>
                <div class="summary-label">Total Penawaran</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="summary-box summary-box-2">
                <div class="summary-number"><?= $stats['draft'] ?? 0 ?></div>
                <div class="summary-label">Draft</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="summary-box summary-box-3">
                <div class="summary-number"><?= $stats['sent'] ?? 0 ?></div>
                <div class="summary-label">Terkirim</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="summary-box summary-box-4">
                <div class="summary-number"><?= $stats['diterima'] ?? 0 ?></div>
                <div class="summary-label">Diterima</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="summary-box summary-box-5">
                <div class="summary-number"><?= $stats['ditolak'] ?? 0 ?></div>
                <div class="summary-label">Ditolak</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="summary-box summary-box-6">
                <div class="summary-number"><?= $stats['revisi'] ?? 0 ?></div>
                <div class="summary-label">Revisi</div>
            </div>
        </div>
    </div>

   <!-- Quick Filter Section - Perbaiki semua bagian ini: -->
<div class="quick-filter">
    <div class="row g-3">
        <div class="col-md-3">
            <label for="filterStatus" class="form-label">Status</label>
            <select class="form-select" id="filterStatus">
                <option value="">Semua Status</option>
                <!-- PERBAIKI SEMUA INI -->
                <option value="draft" <?= (request()->getGet('status') == 'draft') ? 'selected' : '' ?>>Draft</option>
                <option value="sent" <?= (request()->getGet('status') == 'sent') ? 'selected' : '' ?>>Terkirim</option>
                <option value="revisi" <?= (request()->getGet('status') == 'revisi') ? 'selected' : '' ?>>Revisi</option>
                <option value="diterima" <?= (request()->getGet('status') == 'diterima') ? 'selected' : '' ?>>Diterima</option>
                <option value="ditolak" <?= (request()->getGet('status') == 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                <option value="kadaluarsa" <?= (request()->getGet('status') == 'kadaluarsa') ? 'selected' : '' ?>>Kadaluarsa</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="filterMonth" class="form-label">Bulan</label>
            <select class="form-select" id="filterMonth">
                <option value="">Semua Bulan</option>
                <?php
                $months = [
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                ];
                foreach ($months as $key => $month) {
                    // PERBAIKI INI
                    $selected = (request()->getGet('month') == $key) ? 'selected' : '';
                    echo "<option value='$key' $selected>$month</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="filterYear" class="form-label">Tahun</label>
            <select class="form-select" id="filterYear">
                <option value="">Semua Tahun</option>
                <?php
                $currentYear = date('Y');
                for ($year = $currentYear; $year >= $currentYear - 3; $year--) {
                    // PERBAIKI INI
                    $selected = (request()->getGet('year') == $year) ? 'selected' : '';
                    echo "<option value='$year' $selected>$year</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="searchKeyword" class="form-label">Cari</label>
            <input type="text" class="form-control" id="searchKeyword" 
                   placeholder="Nomor/Client/Project..." 
                   
                   value="<?= esc(request()->getGet('search') ?? '') ?>">
        </div>
    </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-modern-primary" onclick="applyFilters()">
                            <i class="fas fa-search me-1"></i> Terapkan Filter
                        </button>
                        <button type="button" class="btn btn-modern-outline ms-2" onclick="resetFilters()">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                        <label class="form-check-label" for="autoRefresh">Auto Refresh (5 menit)</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Penawaran Table -->
    <div class="row">
        <div class="col-12">
            <div class="penawaran-card">
                <div class="card-header-penawaran">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-title-penawaran">Daftar Penawaran</div>
                            <small class="opacity-75">Total <?= $stats['total'] ?? 0 ?> penawaran</small>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-file-contract fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-penawaran">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Penawaran</th>
                                    <th>Client</th>
                                    <th>Project</th>
                                    <th>Tanggal</th>
                                    <th>Nilai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($penawaran)): ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($penawaran as $row): ?>
                                        <?php
                                        // Status mapping
                                        $statusClass = '';
                                        $statusText = '';
                                        
                                        switch (strtolower($row['status'])) {
                                            case 'draft':
                                                $statusClass = 'status-draft';
                                                $statusText = 'Draft';
                                                break;
                                            case 'sent':
                                                $statusClass = 'status-sent';
                                                $statusText = 'Terkirim';
                                                break;
                                            case 'revisi':
                                                $statusClass = 'status-revisi';
                                                $statusText = 'Revisi';
                                                break;
                                            case 'diterima':
                                                $statusClass = 'status-diterima';
                                                $statusText = 'Diterima';
                                                break;
                                            case 'ditolak':
                                                $statusClass = 'status-ditolak';
                                                $statusText = 'Ditolak';
                                                break;
                                            case 'kadaluarsa':
                                                $statusClass = 'status-kadaluarsa';
                                                $statusText = 'Kadaluarsa';
                                                break;
                                            default:
                                                $statusClass = 'status-draft';
                                                $statusText = 'Draft';
                                        }
                                        
                                        // Value indicator
                                        $totalValue = floatval($row['total'] ?? 0);
                                        $valueClass = 'value-medium';
                                        if ($totalValue >= 1000000000) {
                                            $valueClass = 'value-high';
                                        } elseif ($totalValue <= 100000000) {
                                            $valueClass = 'value-low';
                                        }
                                        
                                        // Format dates
                                        $tanggalPenawaran = !empty($row['tanggal_penawaran']) ? 
                                            date('d/m/Y', strtotime($row['tanggal_penawaran'])) : '';
                                        $tanggalKadaluarsa = !empty($row['tanggal_kadaluarsa']) ? 
                                            date('d/m/Y', strtotime($row['tanggal_kadaluarsa'])) : '';
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <strong><?= $row['nomor_penawaran'] ?? '-' ?></strong>
                                                <?php if (!empty($row['quot_format'])): ?>
                                                    <br><small class="text-muted">Format: <?= $row['quot_format'] ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="client-logo">
                                                        <?= strtoupper(substr($row['client_nama'] ?? 'C', 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <strong><?= $row['client_nama'] ?? '-' ?></strong>
                                                        <?php if (!empty($row['kode_client'])): ?>
                                                            <br><small class="text-muted"><?= $row['kode_client'] ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong><?= $row['nama_project'] ?? '-' ?></strong>
                                                <?php if (!empty($row['kode_project'])): ?>
                                                    <br><small class="text-muted"><?= $row['kode_project'] ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $tanggalPenawaran ?>
                                                <?php if (!empty($tanggalKadaluarsa)): ?>
                                                    <br>
                                                    <small class="text-danger">
                                                        Exp: <?= $tanggalKadaluarsa ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="value-indicator <?= $valueClass ?>">
                                                    Rp <?= number_format($totalValue, 0, ',', '.') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge <?= $statusClass ?>">
                                                    <?= $statusText ?>
                                                </span>
                                                <?php if (!empty($row['created_by_name'])): ?>
                                                    <br><small class="text-muted">By: <?= $row['created_by_name'] ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-btn-group">
                                                    <!-- View Button -->
                                                    <a href="<?= base_url('direktur/penawaran/detail/' . $row['id']) ?>" 
                                                       class="btn btn-sm btn-info action-btn" 
                                                       title="Detail"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <!-- Edit Button -->
                                                    <?php if (in_array(strtolower($row['status']), ['draft', 'revisi'])): ?>
                                                        <a href="<?= base_url('direktur/penawaran/edit/' . $row['id']) ?>" 
                                                           class="btn btn-sm btn-warning action-btn" 
                                                           title="Edit"
                                                           data-bs-toggle="tooltip">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Approve Button -->
                                                    <?php if (in_array(strtolower($row['status']), ['draft', 'sent', 'revisi'])): ?>
                                                        <a href="<?= base_url('direktur/penawaran/approve/' . $row['id']) ?>" 
                                                           class="btn btn-sm btn-success action-btn" 
                                                           title="Approve"
                                                           data-bs-toggle="tooltip"
                                                           onclick="return confirm('Approve penawaran ini?')">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Reject Button -->
                                                    <?php if (in_array(strtolower($row['status']), ['draft', 'sent', 'revisi'])): ?>
                                                        <a href="<?= base_url('direktur/penawaran/reject/' . $row['id']) ?>" 
                                                           class="btn btn-sm btn-danger action-btn" 
                                                           title="Reject"
                                                           data-bs-toggle="tooltip"
                                                           onclick="return confirm('Tolak penawaran ini?')">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Print Button -->
                                                    <?php if (strtolower($row['status']) === 'diterima'): ?>
                                                        <a href="<?= base_url('direktur/penawaran/print/' . $row['id']) ?>" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-primary action-btn" 
                                                           title="Print"
                                                           data-bs-toggle="tooltip">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Delete Button -->
                                                    <?php if (in_array(strtolower($row['status']), ['draft', 'revisi'])): ?>
                                                        <form action="<?= base_url('direktur/penawaran/delete/' . $row['id']) ?>" 
                                                              method="POST" style="display: inline;" 
                                                              onsubmit="return confirm('Hapus penawaran ini?')">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <button type="submit" class="btn btn-sm btn-danger action-btn" 
                                                                    title="Hapus" data-bs-toggle="tooltip">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Export Button -->
                                                    <a href="<?= base_url('direktur/penawaran/export/pdf/' . $row['id']) ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-secondary action-btn" 
                                                       title="Export PDF"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                                Tidak ada data penawaran
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
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="penawaran-card">
                <div class="card-body">
                    <h6 class="card-title text-primary mb-3">
                        <i class="fas fa-chart-pie me-2"></i>Distribusi Status
                    </h6>
                    <div id="statusChart" style="height: 200px;">
                        <canvas id="statusChartCanvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="penawaran-card">
                <div class="card-body">
                    <h6 class="card-title text-primary mb-3">
                        <i class="fas fa-chart-line me-2"></i>Trend Bulanan
                    </h6>
                    <div id="monthlyTrendChart" style="height: 200px;">
                        <canvas id="trendChartCanvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="penawaran-card">
                <div class="card-body">
                    <h6 class="card-title text-primary mb-3">
                        <i class="fas fa-trophy me-2"></i>Top Performers
                    </h6>
                    <div class="list-group list-group-flush">
                        <?php
                        // Get top performers from data
                        $topPerformers = [];
                        if (!empty($penawaran)) {
                            $performers = [];
                            foreach ($penawaran as $row) {
                                if (!empty($row['created_by_name'])) {
                                    $name = $row['created_by_name'];
                                    if (!isset($performers[$name])) {
                                        $performers[$name] = [
                                            'name' => $name,
                                            'count' => 0,
                                            'total_value' => 0
                                        ];
                                    }
                                    $performers[$name]['count']++;
                                    $performers[$name]['total_value'] += floatval($row['total'] ?? 0);
                                }
                            }
                            
                            // Sort by count
                            usort($performers, function($a, $b) {
                                return $b['count'] - $a['count'];
                            });
                            
                            $topPerformers = array_slice($performers, 0, 3);
                        }
                        ?>
                        
                        <?php if (!empty($topPerformers)): ?>
                            <?php foreach ($topPerformers as $performer): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= $performer['name'] ?></strong>
                                        <br><small class="text-muted">Rp <?= number_format($performer['total_value'], 0, ',', '.') ?></small>
                                    </div>
                                    <span class="badge bg-success"><?= $performer['count'] ?> deals</span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-center text-muted">
                                <i class="fas fa-users me-1"></i> No data available
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize charts
    initializeCharts();
    
    // Auto refresh if enabled
    const autoRefreshCheckbox = document.getElementById('autoRefresh');
    let refreshInterval;
    
    if (autoRefreshCheckbox && autoRefreshCheckbox.checked) {
        refreshInterval = setInterval(refreshPenawaran, 300000); // Refresh every 5 minutes
    }
    
    autoRefreshCheckbox.addEventListener('change', function() {
        if (this.checked) {
            refreshInterval = setInterval(refreshPenawaran, 300000);
        } else {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        }
    });
});

// Initialize charts
function initializeCharts() {
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChartCanvas');
    if (statusCtx) {
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Draft', 'Terkirim', 'Revisi', 'Diterima', 'Ditolak', 'Kadaluarsa'],
                datasets: [{
                    data: [
                        <?= $stats['draft'] ?? 0 ?>,
                        <?= $stats['sent'] ?? 0 ?>,
                        <?= $stats['revisi'] ?? 0 ?>,
                        <?= $stats['diterima'] ?? 0 ?>,
                        <?= $stats['ditolak'] ?? 0 ?>,
                        <?= $stats['kadaluarsa'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        '#6c757d',
                        '#17a2b8',
                        '#ffc107',
                        '#28a745',
                        '#dc3545',
                        '#20c997'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Monthly Trend Chart
    const trendCtx = document.getElementById('trendChartCanvas');
    if (trendCtx) {
        const trendChart = new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Penawaran',
                    data: [<?php 
                        // Monthly data - bisa diambil dari controller nanti
                        $monthlyData = array_fill(0, 12, 0);
                        if (!empty($penawaran)) {
                            foreach ($penawaran as $row) {
                                if (!empty($row['tanggal_penawaran'])) {
                                    $month = date('n', strtotime($row['tanggal_penawaran'])) - 1;
                                    $monthlyData[$month]++;
                                }
                            }
                        }
                        echo implode(',', $monthlyData);
                    ?>],
                    backgroundColor: 'rgba(42, 75, 140, 0.7)',
                    borderColor: '#2a4b8c',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
}

// Filter functions
function applyFilters() {
    const status = document.getElementById('filterStatus').value;
    const month = document.getElementById('filterMonth').value;
    const year = document.getElementById('filterYear').value;
    const keyword = document.getElementById('searchKeyword').value;
    
    // Build query parameters
    let params = new URLSearchParams();
    
    if (status) params.append('status', status);
    if (month) params.append('month', month);
    if (year) params.append('year', year);
    if (keyword) params.append('search', keyword.trim());
    
    // Reload page with filters
    window.location.href = '<?= base_url('direktur/penawaran') ?>?' + params.toString();
}

function resetFilters() {
    window.location.href = '<?= base_url('direktur/penawaran') ?>';
}

function refreshPenawaran() {
    showToast('Memperbarui data penawaran...', 'info');
    location.reload();
}

function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `custom-toast toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '1060';
    toast.style.minWidth = '300px';
    toast.style.maxWidth = '350px';
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
    
    // Initialize Bootstrap toast
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    // Auto remove after hide
    toast.addEventListener('hidden.bs.toast', function () {
        toast.remove();
    });
}

// Auto-submit filter on enter
document.getElementById('searchKeyword').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

// Add animation for toast
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

<?= $this->include('direktur/templates/footer') ?>