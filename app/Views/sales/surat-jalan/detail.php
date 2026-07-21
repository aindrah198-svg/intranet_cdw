<?php
$title = $title ?? 'Detail Surat Jalan';
$active = $active ?? 'surat_jalan';

// Helper untuk data
$sj = $suratJalan ?? [];
$items = $items ?? [];
$itemStats = $itemStats ?? [];
$perusahaanCDW = $perusahaanCDW ?? [];
$perusahaanLogoBase64 = $perusahaanLogoBase64 ?? null;

// Status colors
$statusColors = [
    'draft' => 'secondary',
    'diproses' => 'warning',
    'dikirim' => 'info',
    'diterima' => 'success',
    'ditolak' => 'danger',
    'dibatalkan' => 'dark'
];

// Status text
$statusText = [
    'draft' => 'Draft',
    'diproses' => 'Diproses',
    'dikirim' => 'Dikirim',
    'diterima' => 'Diterima',
    'ditolak' => 'Ditolak',
    'dibatalkan' => 'Dibatalkan'
];

// Status terima colors
$statusTerimaColors = [
    'pending' => 'warning',
    'diterima' => 'success',
    'ditolak' => 'danger'
];

// Status terima text
$statusTerimaText = [
    'pending' => 'Menunggu',
    'diterima' => 'Diterima',
    'ditolak' => 'Ditolak'
];

// Format functions
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date) || $date == '0000-00-00') return '-';
    return date($format, strtotime($date));
}

function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals, ',', '.');
}

function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') return '-';
    return date($format, strtotime($datetime));
}

// Calculate totals
$totalQty = 0;
$totalBerat = 0;
foreach ($items as $item) {
    $totalQty += $item['qty'];
    $totalBerat += $item['berat'];
}
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
                    <?= $subtitle ?? 'Informasi Lengkap Surat Jalan - Format Manual' ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-start border-5 border-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Status
                            </div>
                            <div class="h6 mb-0 fw-bold text-gray-800">
                                <span class="badge bg-<?= $statusColors[$sj['status']] ?? 'secondary' ?>">
                                    <?= $statusText[$sj['status']] ?? $sj['status'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-start border-5 border-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Status Terima
                            </div>
                            <div class="h6 mb-0 fw-bold text-gray-800">
                                <span class="badge bg-<?= $statusTerimaColors[$sj['status_terima'] ?? 'pending'] ?>">
                                    <?= $statusTerimaText[$sj['status_terima'] ?? 'pending'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-start border-5 border-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Total Barang
                            </div>
                            <div class="h6 mb-0 fw-bold text-gray-800">
                                <?= $itemStats['total_items'] ?? count($items) ?> item(s)
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-start border-5 border-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Total Qty
                            </div>
                            <div class="h6 mb-0 fw-bold text-gray-800">
                                <?= formatNumber($totalQty) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-start border-5 border-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-dark text-uppercase mb-1">
                                Total Berat
                            </div>
                            <div class="h6 mb-0 fw-bold text-gray-800">
                                <?= formatNumber($totalBerat, 2) ?> kg
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-weight-hanging fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-start border-5 border-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-secondary text-uppercase mb-1">
                                Dibuat Oleh
                            </div>
                            <div class="h6 mb-0 fw-bold text-gray-800">
                                <?= htmlspecialchars($sj['created_by_name'] ?? 'System') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= base_url('sales/surat-jalan') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
                        </a>
                        
                        <?php if (in_array($sj['status'], ['draft', 'diproses', 'dikirim'])): ?>
                        <a href="<?= base_url('sales/surat-jalan/edit/' . $sj['id']) ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Edit Surat Jalan
                        </a>
                        <?php endif; ?>
                        
                        <a href="<?= base_url('sales/surat-jalan/cetak/' . $sj['id']) ?>" 
                           target="_blank" 
                           class="btn btn-success">
                            <i class="fas fa-print me-2"></i> Cetak Surat Jalan
                        </a>
                        
                        <a href="<?= base_url('sales/surat-jalan/exportPdf/' . $sj['id']) ?>" 
                           class="btn btn-danger">
                            <i class="fas fa-file-pdf me-2"></i> Export PDF
                        </a>
                        
                        <a href="<?= base_url('sales/surat-jalan/exportExcel/' . $sj['id']) ?>" 
                           class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </a>
                        
                        <!-- Quick Status Update Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-sync-alt me-2"></i> Update Status
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form action="<?= base_url('sales/surat-jalan/updateStatus/' . $sj['id']) ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="diproses">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-clock me-2 text-warning"></i> Set Diproses
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="<?= base_url('sales/surat-jalan/updateStatus/' . $sj['id']) ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="dikirim">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-truck me-2 text-info"></i> Set Dikirim
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="<?= base_url('sales/surat-jalan/updateStatus/' . $sj['id']) ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="diterima">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-check-circle me-2 text-success"></i> Set Diterima
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="<?= base_url('sales/surat-jalan/updateStatus/' . $sj['id']) ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="ditolak">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-times-circle me-2 text-danger"></i> Set Ditolak
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="<?= base_url('sales/surat-jalan/updateStatus/' . $sj['id']) ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="dibatalkan">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-ban me-2 text-dark"></i> Set Dibatalkan
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        
                        <?php if (in_array($sj['status'], ['draft', 'diproses'])): ?>
                        <a href="<?= base_url('sales/surat-jalan/delete/' . $sj['id']) ?>" 
                           class="btn btn-outline-danger"
                           onclick="return confirm('Yakin ingin menghapus surat jalan ini?')">
                            <i class="fas fa-trash me-2"></i> Hapus
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column - Informasi Surat Jalan -->
        <div class="col-lg-8">
            <!-- Informasi Dasar -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Surat Jalan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Nomor Surat Jalan</strong></td>
                                    <td width="5%">:</td>
                                    <td>
                                        <span class="fw-bold text-primary"><?= htmlspecialchars($sj['nomor_surat_jalan']) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Kirim</strong></td>
                                    <td>:</td>
                                    <td><?= formatDate($sj['tanggal_kirim'], 'd/m/Y') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Project</strong></td>
                                    <td>:</td>
                                    <td>
                                        <span class="fw-bold"><?= htmlspecialchars($sj['kode_project'] ?? '') ?></span> - 
                                        <?= htmlspecialchars($sj['nama_project'] ?? '') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Invoice</strong></td>
                                    <td>:</td>
                                    <td>
                                        <?php if (!empty($sj['nomor_invoice'])): ?>
                                            <?= htmlspecialchars($sj['nomor_invoice']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Status</strong></td>
                                    <td width="5%">:</td>
                                    <td>
                                        <span class="badge bg-<?= $statusColors[$sj['status']] ?? 'secondary' ?>">
                                            <?= $statusText[$sj['status']] ?? $sj['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status Terima</strong></td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge bg-<?= $statusTerimaColors[$sj['status_terima'] ?? 'pending'] ?>">
                                            <?= $statusTerimaText[$sj['status_terima'] ?? 'pending'] ?>
                                        </span>
                                        <?php if (!empty($sj['tanggal_terima'])): ?>
                                            <br>
                                            <small class="text-muted"><?= formatDateTime($sj['tanggal_terima']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($sj['created_by_name'] ?? 'System') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Diupdate</strong></td>
                                    <td>:</td>
                                    <td><?= formatDateTime($sj['updated_at'] ?? '', 'd/m/Y H:i:s') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Penerima -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Informasi Penerima
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-building me-2"></i> Perusahaan Penerima</h6>
                            <p class="fw-bold mb-1"><?= htmlspecialchars($sj['penerima_perusahaan'] ?? $sj['nama_perusahaan'] ?? '') ?></p>
                            <p class="text-muted mb-0">UP: <?= htmlspecialchars($sj['penerima_up'] ?? $sj['penerima'] ?? '') ?></p>
                            <?php if (!empty($sj['penerima_telepon'])): ?>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-phone me-1"></i> <?= htmlspecialchars($sj['penerima_telepon']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i> Alamat Pengiriman</h6>
                            <p class="mb-1"><?= nl2br(htmlspecialchars($sj['alamat_pengiriman'] ?? '')) ?></p>
                            <?php if (!empty($sj['lokasi_proyek'])): ?>
                                <p class="mt-2 mb-0">
                                    <strong><i class="fas fa-map-pin me-1"></i> Lokasi Proyek:</strong><br>
                                    <?= htmlspecialchars($sj['lokasi_proyek']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barang yang Dikirim -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        Barang yang Dikirim
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Catatan Barang (Naratif) -->
                    <?php if (!empty($sj['catatan_barang'])): ?>
                    <div class="mb-4">
                        <h6 class="mb-3">
                            <i class="fas fa-sticky-note me-2"></i> 
                            Catatan Barang (Deskripsi Naratif)
                        </h6>
                        <div class="alert alert-light border">
                            <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;"><?= htmlspecialchars($sj['catatan_barang']) ?></pre>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tabel Barang Detail -->
                    <?php if (!empty($items)): ?>
                    <h6 class="mb-3">
                        <i class="fas fa-list me-2"></i> 
                        Detail Barang (<?= count($items) ?> item)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="35%">Nama Barang</th>
                                    <th width="10%" class="text-end">Qty</th>
                                    <th width="10%">Satuan</th>
                                    <th width="15%" class="text-end">Berat</th>
                                    <th width="25%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                    <td class="text-end"><?= formatNumber($item['qty']) ?></td>
                                    <td><?= htmlspecialchars($item['satuan']) ?></td>
                                    <td class="text-end">
                                        <?php if ($item['berat'] > 0): ?>
                                            <?= formatNumber($item['berat'], 2) ?> <?= htmlspecialchars($item['satuan_berat'] ?? 'kg') ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($item['keterangan'] ?? '') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <!-- Total Row -->
                                <tr class="table-secondary fw-bold">
                                    <td colspan="2" class="text-center">TOTAL</td>
                                    <td class="text-end"><?= formatNumber($totalQty) ?></td>
                                    <td></td>
                                    <td class="text-end">
                                        <?php if ($totalBerat > 0): ?>
                                            <?= formatNumber($totalBerat, 2) ?> kg
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Tidak ada barang dalam surat jalan ini</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Keterangan Lainnya -->
            <?php if (!empty($sj['keterangan'])): ?>
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>
                        Keterangan Lainnya
                    </h5>
                </div>
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($sj['keterangan'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Informasi Tambahan -->
        <div class="col-lg-4">
            <!-- Informasi Pengiriman -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i>
                        Informasi Pengiriman
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <?php if (!empty($sj['sopir'])): ?>
                        <tr>
                            <td width="40%"><strong>Sopir</strong></td>
                            <td width="5%">:</td>
                            <td><?= htmlspecialchars($sj['sopir']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($sj['sopir_telepon'])): ?>
                        <tr>
                            <td><strong>Telepon Sopir</strong></td>
                            <td>:</td>
                            <td><?= htmlspecialchars($sj['sopir_telepon']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($sj['no_kendaraan'])): ?>
                        <tr>
                            <td><strong>No. Kendaraan</strong></td>
                            <td>:</td>
                            <td>
                                <span class="badge bg-dark"><?= htmlspecialchars($sj['no_kendaraan']) ?></span>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr>
                            <td><strong>Tanggal Terima</strong></td>
                            <td>:</td>
                            <td>
                                <?php if (!empty($sj['tanggal_terima'])): ?>
                                    <?= formatDateTime($sj['tanggal_terima']) ?>
                                <?php else: ?>
                                    <span class="text-muted">Belum diterima</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    
                    <?php if (empty($sj['sopir']) && empty($sj['no_kendaraan'])): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-truck fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Informasi pengiriman belum diisi</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Penandatanganan -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-signature me-2"></i>
                        Penandatanganan
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Disiapkan Oleh -->
                    <div class="mb-4">
                        <h6 class="mb-2">Disiapkan oleh:</h6>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-user me-2 text-primary"></i>
                            <span class="fw-bold"><?= htmlspecialchars($sj['disiapkan_oleh'] ?? '-') ?></span>
                        </div>
                        <?php if (!empty($sj['disiapkan_telepon'])): ?>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-phone me-2 text-primary"></i>
                            <span><?= htmlspecialchars($sj['disiapkan_telepon']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($sj['disiapkan_jabatan'])): ?>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-briefcase me-2 text-primary"></i>
                            <span><?= htmlspecialchars($sj['disiapkan_jabatan']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Dikirim Oleh -->
                    <div class="mb-4">
                        <h6 class="mb-2">Dikirim oleh:</h6>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-user me-2 text-warning"></i>
                            <span class="fw-bold"><?= htmlspecialchars($sj['dikirim_oleh'] ?? $sj['sopir'] ?? '-') ?></span>
                        </div>
                        <?php if (!empty($sj['dikirim_telepon'])): ?>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-phone me-2 text-warning"></i>
                            <span><?= htmlspecialchars($sj['dikirim_telepon']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Diterima Oleh -->
                    <div class="mb-0">
                        <h6 class="mb-2">Diterima oleh:</h6>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-user me-2 text-success"></i>
                            <span class="fw-bold"><?= htmlspecialchars($sj['diterima_oleh'] ?? '-') ?></span>
                        </div>
                        <?php if (!empty($sj['diterima_telepon'])): ?>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-phone me-2 text-success"></i>
                            <span><?= htmlspecialchars($sj['diterima_telepon']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-building me-2 text-success"></i>
                            <span><?= htmlspecialchars($sj['diterima_perusahaan'] ?? $sj['penerima_perusahaan'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Client -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Informasi Client
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><?= htmlspecialchars($sj['nama_perusahaan'] ?? '') ?></h6>
                    <?php if (!empty($sj['alamat_pengiriman'])): ?>
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                        <?= nl2br(htmlspecialchars(substr($sj['alamat_pengiriman'], 0, 100))) ?>...
                    </p>
                    <?php endif; ?>
                    
                    <div class="row mt-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Kontak</small>
                            <?php if (!empty($sj['telepon_client'])): ?>
                                <span><?= htmlspecialchars($sj['telepon_client']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Sales</small>
                            <span><?= htmlspecialchars($sj['nama_sales'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('sales/surat-jalan/cetak/' . $sj['id']) ?>" 
                           target="_blank"
                           class="btn btn-outline-success">
                            <i class="fas fa-print me-2"></i> Cetak Sekarang
                        </a>
                        
                        <a href="<?= base_url('sales/surat-jalan/exportPdf/' . $sj['id']) ?>" 
                           class="btn btn-outline-danger">
                            <i class="fas fa-file-pdf me-2"></i> Download PDF
                        </a>
                        
                        <a href="<?= base_url('sales/surat-jalan/exportExcel/' . $sj['id']) ?>" 
                           class="btn btn-outline-success">
                            <i class="fas fa-file-excel me-2"></i> Download Excel
                        </a>
                        
                        <?php if (in_array($sj['status'], ['draft', 'diproses', 'dikirim'])): ?>
                        <a href="<?= base_url('sales/surat-jalan/edit/' . $sj['id']) ?>" 
                           class="btn btn-outline-warning">
                            <i class="fas fa-edit me-2"></i> Edit Surat Jalan
                        </a>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="fas fa-share-alt me-2"></i> Bagikan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bagikan Surat Jalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Link Surat Jalan</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareLink" 
                               value="<?= base_url('sales/surat-jalan/detail/' . $sj['id']) ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyShareLink()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Link Cetak (PDF)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="pdfLink" 
                               value="<?= base_url('sales/surat-jalan/cetakPdf/' . $sj['id']) ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyPdfLink()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Bagikan via:</h6>
                    <div class="d-flex gap-2">
                        <a href="mailto:?subject=Surat Jalan <?= urlencode($sj['nomor_surat_jalan']) ?>&body=<?= urlencode('Silakan lihat surat jalan: ' . base_url('sales/surat-jalan/detail/' . $sj['id'])) ?>" 
                           class="btn btn-outline-primary flex-fill">
                            <i class="fas fa-envelope me-2"></i> Email
                        </a>
                        <a href="https://wa.me/?text=<?= urlencode('Surat Jalan ' . $sj['nomor_surat_jalan'] . ' - ' . base_url('sales/surat-jalan/detail/' . $sj['id'])) ?>" 
                           target="_blank"
                           class="btn btn-outline-success flex-fill">
                            <i class="fab fa-whatsapp me-2"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.85em;
    padding: 0.4em 0.8em;
}

.text-xs {
    font-size: 0.7rem;
}

.btn-group .btn {
    border-radius: 4px !important;
}

.dropdown-menu form {
    margin: 0;
}

.dropdown-menu button {
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    padding: 0.5rem 1rem;
}

.dropdown-menu button:hover {
    background-color: #f8f9fa;
}

pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 5px;
    font-size: 0.9rem;
}

.table-borderless td {
    padding: 0.3rem 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Copy share link
    window.copyShareLink = function() {
        const shareLink = document.getElementById('shareLink');
        shareLink.select();
        shareLink.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');
        
        // Show tooltip or notification
        alert('Link berhasil disalin ke clipboard!');
    }
    
    // Copy PDF link
    window.copyPdfLink = function() {
        const pdfLink = document.getElementById('pdfLink');
        pdfLink.select();
        pdfLink.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        alert('Link PDF berhasil disalin ke clipboard!');
    }
    
    // Confirm status update
    document.querySelectorAll('form[action*="updateStatus"] button').forEach(button => {
        button.addEventListener('click', function(e) {
            const form = this.closest('form');
            const status = form.querySelector('input[name="status"]').value;
            const statusText = this.textContent.trim();
            
            if (!confirm(`Yakin ingin mengubah status menjadi "${statusText}"?`)) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            
            // Submit form
            return true;
        });
    });
    
    // Print button functionality
    document.querySelectorAll('a[href*="cetak/"]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.getAttribute('target')) {
                e.preventDefault();
                window.open(this.href, '_blank', 'width=800,height=600');
            }
        });
    });
});
</script>

<!-- Font Awesome for WhatsApp icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">