<?php
// app/Views/direktur/pengadaan/kerusakan_detail.php

$title = $title ?? 'Detail Laporan Kerusakan Alat';
$templateData = [
    'title' => $title,
    'active' => 'pengadaan'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);

$tk = strtolower($k['tingkat_kerusakan'] ?? '');
$pillClass = 'status-pill-ringan';
if ($tk === 'sedang') $pillClass = 'status-pill-sedang';
if ($tk === 'berat') $pillClass = 'status-pill-berat';

$st = strtolower($k['status_tindakan'] ?? 'dilaporkan');
$stBg = 'bg-secondary';
$stLabel = 'Dilaporkan';
if ($st === 'dalam_perbaikan' || $st === 'proses_perbaikan') {
    $stBg = 'bg-primary';
    $stLabel = 'Dalam Perbaikan / Servis';
} elseif ($st === 'selesai') {
    $stBg = 'bg-success';
    $stLabel = 'Selesai Diperbaiki';
} elseif ($st === 'rusak_total') {
    $stBg = 'bg-danger';
    $stLabel = 'Rusak Total / Ganti Unit';
}
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/pengadaan/kerusakan') ?>" class="text-decoration-none text-muted">Kerusakan Alat</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail #<?= esc($k['kode_laporan']) ?></li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-info-circle text-info me-2"></i> Rincian Laporan Kerusakan Alat</h4>
            <small class="text-muted">Informasi rincian kerusakan, lokasi asal, teknisi pengurus, dan perkembangan perbaikan.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/pengadaan/kerusakan/edit/'.$k['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Laporan
            </a>
            <a href="<?= base_url('direktur/pengadaan/kerusakan') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                
                <!-- Hero Header -->
                <div class="card-header bg-gradient-primary text-white p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <span class="badge bg-white text-primary rounded-pill px-3 py-1.5 fw-bold text-xs mb-2 d-inline-block">
                                <i class="fas fa-barcode me-1"></i> <?= esc($k['kode_laporan']) ?>
                            </span>
                            <h3 class="fw-bold text-white mb-1"><?= esc($k['nama_alat']) ?></h3>
                            <small class="text-white-50"><i class="fas fa-user-edit me-1"></i> Pelapor: <strong><?= esc($k['pelapor'] ?? 'Direktur') ?></strong> (<?= esc($k['pelapor_jabatan'] ?: 'Manajemen') ?>)</small>
                        </div>
                        <div class="text-end">
                            <span class="badge <?= $stBg ?> fs-6 px-3 py-2 rounded-pill shadow-sm mb-2 d-inline-block">
                                <?= $stLabel ?>
                            </span>
                            <div class="text-xs text-white-50">Tingkat Kerusakan:</div>
                            <span class="status-pill <?= $pillClass ?> bg-white text-dark shadow-sm">
                                <i class="fas fa-circle fs-6 me-1.5" style="font-size: 0.4rem !important;"></i>
                                <?= strtoupper(esc($k['tingkat_kerusakan'])) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    
                    <!-- Section 1: Lokasi & Deskripsi -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> Lokasi Asal Alat</small>
                                <h6 class="fw-bold text-dark mb-0"><?= esc($k['lokasi_alat'] ?: '- Tidak ditentukan -') ?></h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-calendar-alt text-primary me-1"></i> Tanggal Dilaporkan</small>
                                <h6 class="fw-bold text-dark mb-0"><?= date('d F Y, H:i', strtotime($k['created_at'])) ?> WIB</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Workflow Penugasan -->
                    <div class="p-4 bg-light rounded-4 border mb-4">
                        <h6 class="fw-bold text-dark fs-6 mb-3 border-bottom pb-2"><i class="fas fa-route text-primary me-2"></i> Alur Penugasan & Lokasi Perbaikan</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-user-cog text-warning me-1"></i> Diservis / Benerin Oleh</small>
                                <h6 class="fw-bold text-dark mb-0"><?= esc($k['teknisi_pengurus'] ?: 'Belum ditentukan') ?></h6>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-location-dot text-info me-1"></i> Dibawa Ke (Tujuan Servis)</small>
                                <h6 class="fw-bold text-dark mb-0"><?= esc($k['lokasi_perbaikan'] ?: 'Workshop IT') ?></h6>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-truck text-success me-1"></i> Dibawa Oleh (Kurir/Pembawa)</small>
                                <h6 class="fw-bold text-dark mb-0"><?= esc($k['petugas_pembawa'] ?: 'Petugas GA / Self Deliver') ?></h6>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Kronologi & Catatan -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-align-left text-primary me-2"></i> Deskripsi Kronologi Kerusakan</h6>
                        <div class="p-3 bg-white rounded-3 border text-dark text-sm">
                            <?= nl2br(esc($k['deskripsi_kerusakan'])) ?>
                        </div>
                    </div>

                    <div class="mb-2">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-clipboard-check text-success me-2"></i> Catatan & Perkembangan Perbaikan</h6>
                        <div class="p-3 bg-white rounded-3 border text-dark text-sm">
                            <?= !empty($k['catatan_perbaikan']) ? nl2br(esc($k['catatan_perbaikan'])) : '<span class="text-muted italic">- Belum ada catatan perkembangan -</span>' ?>
                        </div>
                    </div>

                </div>

                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('direktur/pengadaan/kerusakan') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <a href="<?= base_url('direktur/pengadaan/kerusakan/edit/'.$k['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-edit me-1.5"></i> Edit Laporan Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $templateData) ?>
