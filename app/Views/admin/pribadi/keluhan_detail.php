<?php
$data = [
    'title'    => 'Detail Keluhan Saya',
    'subtitle' => 'Pratinjau Rincian Keluhan & Tanggapan Manajemen',
    'active'   => 'keluhan-saya',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/keluhan-saya') ?>" class="text-decoration-none text-muted">Keluhan Saya</a></li>
                    <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">Detail Keluhan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-comment-dots text-danger me-2"></i> Rincian Keluhan Saya</h4>
            <small class="text-muted">Pratinjau detail keluhan yang pernah Anda sampaikan dan tanggapan dari Direktur/Manajemen.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/keluhan-saya') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('admin/keluhan-saya/edit/'.$k['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Keluhan
            </a>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3.5 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #d32f2f, #b71c1c);">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><i class="fas fa-comment-dots me-2"></i> <?= esc($k['judul']) ?></h5>
                        <small class="text-white-50"><i class="fas fa-tags me-1"></i> Kategori: <?= esc($k['kategori']) ?></small>
                    </div>
                    <?php
                        $st = strtolower($k['status'] ?? 'menunggu');
                        $badge = 'bg-warning text-dark';
                        if ($st === 'diproses') $badge = 'bg-info text-white';
                        if ($st === 'selesai') $badge = 'bg-success text-white';
                        if ($st === 'ditolak') $badge = 'bg-danger text-white';
                    ?>
                    <span class="badge <?= $badge ?> rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= strtoupper(esc($k['status'] ?? 'Menunggu')) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-calendar-alt me-1 text-danger"></i> Tanggal Pengiriman</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= date('d M Y', strtotime($k['tanggal'])) ?></h6>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-info-circle me-1 text-primary"></i> Status Penanganan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($k['status']) ?></h6>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-align-left text-danger me-2"></i> Rincian Keluhan / Aspirasi</h6>
                        <div class="p-3 bg-light rounded-3 border text-dark text-sm" style="line-height: 1.6;">
                            <?= nl2br(esc($k['deskripsi'] ?: 'Tidak ada deskripsi rincian.')) ?>
                        </div>
                    </div>

                    <div class="p-3 rounded-4 border bg-danger bg-opacity-10">
                        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-reply text-danger me-2"></i> Tanggapan dari Direktur / Manajemen</h6>
                        <?php if (!empty($k['tanggapan'])): ?>
                            <small class="text-muted d-block mb-2">Ditanggapi oleh: <strong><?= esc($k['ditanggapi_oleh'] ?: 'Direktur Utama') ?></strong> pada <?= !empty($k['tanggal_tanggapan']) ? date('d M Y H:i', strtotime($k['tanggal_tanggapan'])) : '-' ?></small>
                            <div class="p-3 bg-white rounded-3 border text-dark text-sm fw-semibold">
                                <?= nl2br(esc($k['tanggapan'])) ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted text-xs italic py-2">
                                <i class="fas fa-clock me-1"></i> Keluhan ini sedang menunggu tanggapan atau evaluasi dari Direktur/Manajemen.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('admin/keluhan-saya') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <a href="<?= base_url('admin/keluhan-saya/edit/'.$k['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-edit me-1.5"></i> Edit Keluhan Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
