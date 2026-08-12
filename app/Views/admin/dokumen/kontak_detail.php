<?php
$title = $title ?? 'Detail Kontak Project';
$data = [
    'title'  => $title,
    'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin'],
    'active' => 'dokumen'
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);

$rawHp = preg_replace('/[^0-9]/', '', $k['telepon'] ?? '');
if (substr($rawHp, 0, 1) === '0') {
    $rawHp = '62' . substr($rawHp, 1);
}
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dokumen/kontak') ?>" class="text-decoration-none text-muted">Kontak Project</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Kontak</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-address-book text-info me-2"></i> Rincian Kontak PIC Stakeholder</h4>
            <small class="text-muted">Pratinjau lengkap informasi PIC Klien, akses pintasan WhatsApp & Email, dan catatan project.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/dokumen/kontak') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('admin/dokumen/kontak/edit/'.$k['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Kontak
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-primary text-white py-3.5 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><i class="fas fa-user-circle me-2"></i> <?= esc($k['nama_kontak']) ?></h5>
                        <small class="text-white-50"><i class="fas fa-building me-1"></i> Perusahaan: <?= esc($k['perusahaan_klien'] ?: '-') ?></small>
                    </div>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= esc($k['jabatan'] ?: 'PIC') ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-user-tie me-1 text-primary"></i> Jabatan / Peran</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($k['jabatan'] ?: 'PIC Klien / Stakeholder') ?></h6>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-project-diagram me-1 text-warning"></i> Project Terkait</small>
                                <?php if (!empty($k['nama_project'])): ?>
                                    <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($k['nama_project']) ?></h6>
                                    <small class="text-muted text-xs"><?= esc($k['kode_project'] ?: '') ?></small>
                                <?php else: ?>
                                    <h6 class="fw-bold text-muted mb-0 mt-1">Non-Project / General</h6>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-building me-1 text-info"></i> Perusahaan / Klien</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($k['perusahaan_klien'] ?: '-') ?></h6>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-4 d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fab fa-whatsapp me-1 text-success fs-6"></i> WhatsApp / Telepon</small>
                                    <h5 class="fw-bold text-success mb-0"><?= esc($k['telepon'] ?: '-') ?></h5>
                                </div>
                                <?php if(!empty($k['telepon'])): ?>
                                    <a href="https://wa.me/<?= $rawHp ?>" target="_blank" class="btn btn-success rounded-pill px-3.5 py-1.5 font-semibold text-sm shadow-sm">
                                        <i class="fab fa-whatsapp me-1"></i> Chat WhatsApp
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-4 d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-envelope me-1 text-primary fs-6"></i> Email Resmi</small>
                                    <h6 class="fw-bold text-primary mb-0 text-truncate" style="max-width: 180px;"><?= esc($k['email'] ?: '-') ?></h6>
                                </div>
                                <?php if(!empty($k['email'])): ?>
                                    <a href="mailto:<?= esc($k['email']) ?>" class="btn btn-primary rounded-pill px-3.5 py-1.5 font-semibold text-sm shadow-sm">
                                        <i class="fas fa-paper-plane me-1"></i> Kirim Email
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-sticky-note text-primary me-2"></i> Catatan Khusus Coordination / BAST / Invoice</h6>
                        <div class="p-3 bg-light rounded-3 border text-muted text-sm">
                            <?= nl2br(esc($k['catatan'] ?: 'Tidak ada catatan khusus untuk kontak PIC ini.')) ?>
                        </div>
                    </div>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('admin/dokumen/kontak') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <a href="<?= base_url('admin/dokumen/kontak/edit/'.$k['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-edit me-1.5"></i> Edit Kontak Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
