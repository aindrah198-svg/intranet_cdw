<?php
// app/Views/direktur/dokumen/sertifikat_detail.php

$title = $title ?? 'Detail Dokumen Sertifikat';
$templateData = [
    'title' => $title,
    'active' => 'dokumen'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/dokumen/sertifikat') ?>" class="text-decoration-none text-muted">Dokumen Sertifikat</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Sertifikat</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-award text-info me-2"></i> Rincian Dokumen Sertifikat</h4>
            <small class="text-muted">Pratinjau detail informasi sertifikasi, lembaga penerbit, pemegang, dan berkas fisik.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/dokumen/sertifikat') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('direktur/dokumen/sertifikat/edit/'.$s['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Sertifikat
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-gradient-primary text-white py-3.5 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><?= esc($s['nama_sertifikat']) ?></h5>
                        <small class="text-white-50"><i class="fas fa-barcode me-1"></i> Nomor: <?= esc($s['nomor_sertifikat'] ?: '-') ?></small>
                    </div>
                    <?php
                        $st = strtolower($s['status'] ?? 'aktif');
                        $badge = 'bg-white text-success';
                        if ($st == 'kadaluarsa') $badge = 'bg-white text-danger';
                        if ($st == 'proses_perpanjangan') $badge = 'bg-white text-warning';
                    ?>
                    <span class="badge <?= $badge ?> rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= strtoupper(esc($s['status'])) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-building me-1 text-primary"></i> Lembaga Penerbit</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($s['penerbit']) ?></h6>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-user-tie me-1 text-info"></i> Pemegang Sertifikat</small>
                                <?php if (!empty($s['karyawan'])): ?>
                                    <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($s['karyawan']) ?></h6>
                                    <small class="text-muted text-xs"><?= esc($s['karyawan_jabatan'] ?? '') ?></small>
                                <?php else: ?>
                                    <h6 class="fw-bold text-primary mb-0 mt-1">Perusahaan (Corporate)</h6>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-clock me-1 text-warning"></i> Masa Berlaku</small>
                                <div class="mt-1">
                                    <?php if(!empty($s['masa_berlaku'])): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 text-xs fw-bold">
                                            s/d <?= date('d F Y', strtotime($s['masa_berlaku'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success text-white rounded-pill px-2.5 py-1 text-xs fw-bold">
                                            <i class="fas fa-infinity me-1"></i> Permanen
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-calendar-alt me-1 text-secondary"></i> Tanggal Perolehan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= !empty($s['tanggal_perolehan']) ? date('d F Y', strtotime($s['tanggal_perolehan'])) : '-' ?></h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-calendar-check me-1 text-success"></i> Terdaftar Sistem</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= date('d F Y, H:i', strtotime($s['created_at'] ?? 'now')) ?> WIB</h6>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-paperclip text-primary me-2"></i> File Sertifikat Digital</h6>
                        <?php if(!empty($s['file_path'])): ?>
                            <div class="p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-certificate fs-2 text-primary me-3"></i>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0"><?= esc($s['file_path']) ?></h6>
                                        <small class="text-muted text-xs">Arsip digital resmi sertifikasi</small>
                                    </div>
                                </div>
                                <div>
                                    <a href="<?= base_url('uploads/sertifikat/'.$s['file_path']) ?>" target="_blank" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm">
                                        <i class="fas fa-eye me-1.5"></i> Lihat / Unduh Sertifikat
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-secondary rounded-4 border-0 p-3 mb-0 text-muted">
                                <i class="fas fa-exclamation-circle me-2"></i> Belum ada file fisik yang diunggah untuk sertifikat ini.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('direktur/dokumen/sertifikat') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <a href="<?= base_url('direktur/dokumen/sertifikat/edit/'.$s['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-edit me-1.5"></i> Edit Sertifikat Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $templateData) ?>
