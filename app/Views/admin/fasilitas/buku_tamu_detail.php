<?php
$data = [
    'title'    => $title ?? 'Detail Kunjungan Tamu',
    'subtitle' => 'Rincian Lengkap Pencatatan Tamu Kantor CDW Engineering',
    'active'   => 'buku-tamu',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);

$rawHp = preg_replace('/[^0-9]/', '', $t['telepon'] ?? '');
if (substr($rawHp, 0, 1) === '0') {
    $rawHp = '62' . substr($rawHp, 1);
}
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Breadcrumb & Title -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/fasilitas/buku-tamu') ?>" class="text-decoration-none text-muted">Buku Tamu</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Tamu</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-id-badge text-primary me-2"></i> Rincian Kunjungan Tamu</h4>
            <small class="text-muted">Pratinjau lengkap informasi identitas tamu, instansi, jam kedatangan, dan kontak.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/fasilitas/buku-tamu') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('admin/fasilitas/buku-tamu/edit/'.$t['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Data Tamu
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3.5 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #00695c, #00897b);">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><i class="fas fa-user-check me-2"></i> <?= esc($t['nama_tamu']) ?></h5>
                        <small class="text-white-50"><i class="fas fa-building me-1"></i> Instansi: <?= esc($t['instansi'] ?: '-') ?></small>
                    </div>
                    <?php
                        $st = strtolower($t['status'] ?? 'bertemu');
                        $badge = 'bg-info text-dark';
                        if ($st === 'selesai') $badge = 'bg-success text-white';
                        if ($st === 'menunggu') $badge = 'bg-warning text-dark';
                    ?>
                    <span class="badge <?= $badge ?> rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= strtoupper(esc($t['status'])) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-clock me-1 text-primary"></i> Waktu Kedatangan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= date('d M Y', strtotime($t['tanggal_jam'])) ?></h6>
                                <small class="text-muted text-xs"><?= date('H:i', strtotime($t['tanggal_jam'])) ?> WIB</small>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-user-tie me-1 text-info"></i> Bertemu Dengan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($t['bertemu_dengan']) ?></h6>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-building me-1 text-success"></i> Instansi / Perusahaan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($t['instansi'] ?: '-') ?></h6>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fab fa-whatsapp me-1 text-success fs-6"></i> Telepon / WhatsApp Tamu</small>
                                <h5 class="fw-bold text-success mb-0"><?= esc($t['telepon'] ?: '-') ?></h5>
                            </div>
                            <?php if(!empty($t['telepon'])): ?>
                                <a href="https://wa.me/<?= $rawHp ?>" target="_blank" class="btn btn-success rounded-pill px-3.5 py-1.5 font-semibold text-sm shadow-sm">
                                    <i class="fab fa-whatsapp me-1"></i> Chat WhatsApp
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-sticky-note text-primary me-2"></i> Maksud / Keperluan Kunjungan</h6>
                        <div class="p-3 bg-light rounded-3 border text-dark text-sm" style="line-height: 1.6;">
                            <?= nl2br(esc($t['keperluan'] ?: 'Tidak ada rincian khusus.')) ?>
                        </div>
                    </div>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('admin/fasilitas/buku-tamu') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <a href="<?= base_url('admin/fasilitas/buku-tamu/edit/'.$t['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-edit me-1.5"></i> Edit Tamu Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
