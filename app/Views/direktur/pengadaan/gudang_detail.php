<?php
// app/Views/direktur/pengadaan/gudang_detail.php

$title = $title ?? 'Detail Barang Gudang';
$templateData = [
    'title' => $title,
    'active' => 'pengadaan'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);

$st = strtolower($g['status'] ?? 'tersedia');
$pillClass = 'status-pill-tersedia';
$statusText = 'TERSEDIA';
if ($st === 'indent') {
    $pillClass = 'status-pill-indent';
    $statusText = 'INDENT (DALAM PENGIRIMAN)';
} elseif ($st === 'kosong' || $st === 'habis') {
    $pillClass = 'status-pill-kosong';
    $statusText = 'KOSONG / HABIS';
}
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/pengadaan/gudang') ?>" class="text-decoration-none text-muted">Monitoring Gudang</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail #<?= esc($g['kode_barang']) ?></li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-info-circle text-info me-2"></i> Detail Material Gudang</h4>
            <small class="text-muted">Rincian stok barang, foto terkompresi, lokasi gudang & rak.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/pengadaan/gudang/edit/'.$g['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Barang
            </a>
            <a href="<?= base_url('direktur/pengadaan/gudang') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                
                <!-- Hero Header -->
                <div class="card-header bg-gradient-primary text-white p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <span class="badge bg-white text-primary rounded-pill px-3 py-1.5 fw-bold text-xs mb-2 d-inline-block">
                                <i class="fas fa-barcode me-1"></i> <?= esc($g['kode_barang']) ?>
                            </span>
                            <h3 class="fw-bold text-white mb-1"><?= esc($g['nama_barang']) ?></h3>
                            <small class="text-white-50"><i class="fas fa-tags me-1"></i> Kategori: <strong><?= esc($g['kategori'] ?: 'Material') ?></strong></small>
                        </div>
                        <div class="text-end">
                            <div class="text-xs text-white-50 mb-1">Status Stok Fisik:</div>
                            <span class="status-pill <?= $pillClass ?> bg-white text-dark shadow-sm">
                                <i class="fas fa-circle fs-6 me-1.5" style="font-size: 0.4rem !important;"></i>
                                <?= $statusText ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">

                    <!-- Section Foto & Info -->
                    <div class="row g-4 mb-4 align-items-center">
                        <div class="col-md-4 text-center">
                            <?php if (!empty($g['foto_barang']) && file_exists(ROOTPATH . 'public/uploads/gudang/' . $g['foto_barang'])): ?>
                                <img src="<?= base_url('uploads/gudang/' . $g['foto_barang']) ?>" alt="Foto Barang" class="img-fluid rounded-4 shadow-sm border" style="max-height: 220px; width: 100%; object-fit: cover;">
                                <small class="text-muted text-xs d-block mt-1"><i class="fas fa-compress-alt me-1 text-success"></i> Foto Terkompresi (GD/70% Quality)</small>
                            <?php else: ?>
                                <div class="bg-light rounded-4 border p-4 text-center text-muted">
                                    <i class="fas fa-image fs-1 mb-2 text-secondary opacity-50 d-block"></i>
                                    <small class="text-xs font-semibold">Belum ada foto barang</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4 border h-100">
                                        <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-warehouse text-primary me-1"></i> Lokasi Gudang Utama</small>
                                        <h5 class="fw-bold text-dark mb-0"><?= esc($g['lokasi_gudang'] ?: 'Gudang Blok K') ?></h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4 border h-100">
                                        <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-layer-group text-danger me-1"></i> Lokasi Rak / Sektor</small>
                                        <h5 class="fw-bold text-dark mb-0"><?= esc($g['lokasi_rak'] ?: 'Rak A-1') ?></h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4 border h-100">
                                        <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-boxes text-success me-1"></i> Stok Fisik Tersedia</small>
                                        <h3 class="fw-bold text-success mb-0"><?= number_format($g['stok_tersedia']) ?> <span class="fs-6 text-muted font-normal"><?= esc($g['satuan']) ?></span></h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4 border h-100">
                                        <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-clock text-info me-1"></i> Terakhir Diperbarui</small>
                                        <h6 class="fw-bold text-dark mb-0 mt-2"><?= !empty($g['updated_at']) ? date('d F Y, H:i', strtotime($g['updated_at'])) : date('d F Y, H:i', strtotime($g['created_at'] ?? 'now')) ?> WIB</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (strtolower($g['status']) == 'kosong' || strtolower($g['status']) == 'indent'): ?>
                    <div class="alert alert-warning rounded-4 border-0 p-3 shadow-sm d-flex align-items-center gap-3">
                        <i class="fas fa-exclamation-triangle fs-4 text-warning"></i>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Stok Fisik Menipis / Kosong!</h6>
                            <small class="text-muted">Periksa kebutuhan stok atau koordinasikan pemesanan ke bagian pengadaan.</small>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('direktur/pengadaan/gudang') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <a href="<?= base_url('direktur/pengadaan/gudang/edit/'.$g['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-edit me-1.5"></i> Edit Barang Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $templateData) ?>
