<?php
// app/Views/direktur/proyek/baru_detail.php

$title = $title ?? 'Detail Project';
$templateData = [
    'title' => $title,
    'active' => 'proyek'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);

$status = strtolower($p['status']);
$statusBadge = 'bg-info text-dark';
$statusText = 'Penawaran';
if ($status === 'nego') {
    $statusBadge = 'bg-warning text-dark';
    $statusText = 'Negosiasi';
} elseif ($status === 'deal') {
    $statusBadge = 'bg-success text-white';
    $statusText = 'Deal / Sepakat';
} elseif ($status === 'on_progress') {
    $statusBadge = 'bg-primary text-white';
    $statusText = 'On Progress (Sedang Berjalan)';
}
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/proyek/baru') ?>" class="text-decoration-none text-muted">Project Baru</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Project</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-project-diagram text-info me-2"></i> Rincian Detail Project</h4>
            <small class="text-muted">Pratinjau informasi inisiasi project, klien terdaftar, estimasi nilai, dan penunjukan manajer proyek.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/proyek/baru') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('direktur/proyek/edit/'.$p['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Project
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-gradient-primary text-white py-3.5 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><?= esc($p['nama_project']) ?></h5>
                        <small class="text-white-50"><i class="fas fa-barcode me-1"></i> Kode Proyek: <?= esc($p['kode_project']) ?></small>
                    </div>
                    <span class="badge <?= $statusBadge ?> rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= $statusText ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-coins me-1 text-success"></i> Nilai Project (Rp)</small>
                                <h5 class="fw-bold text-success mb-0 mt-1">
                                    Rp <?= number_format($p['nilai_project'] ?? 0, 0, ',', '.') ?>
                                </h5>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-building me-1 text-info"></i> Client / Klien</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($client['nama_perusahaan'] ?? 'Non-Client / General') ?></h6>
                                <small class="text-muted text-xs"><?= esc($client['nama_kontak'] ?? '') ?></small>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-user-tie me-1 text-primary"></i> Project Manager</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($manager['username'] ?? 'Belum Ditunjuk') ?></h6>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-calendar-alt me-1 text-primary"></i> Rencana Tanggal Mulai</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= !empty($p['tanggal_mulai']) ? date('d F Y', strtotime($p['tanggal_mulai'])) : 'Belum Ditentukan' ?></h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-flag-checkered me-1 text-warning"></i> Estimasi Tanggal Selesai</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= !empty($p['tanggal_selesai']) ? date('d F Y', strtotime($p['tanggal_selesai'])) : 'Belum Ditentukan' ?></h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-clock me-1 text-secondary"></i> Tanggal Dibuat</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= date('d F Y, H:i', strtotime($p['created_at'] ?? 'now')) ?> WIB</h6>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-align-left text-primary me-2"></i> Deskripsi Singkat Project</h6>
                        <div class="p-3 bg-light rounded-3 border text-muted text-sm">
                            <?= nl2br(esc($p['deskripsi'] ?: 'Tidak ada deskripsi khusus untuk project ini.')) ?>
                        </div>
                    </div>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('direktur/proyek/baru') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <a href="<?= base_url('direktur/proyek/edit/'.$p['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-edit me-1.5"></i> Edit Project Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $templateData) ?>
