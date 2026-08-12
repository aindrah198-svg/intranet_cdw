<?php
$data = [
    'title'    => $title ?? 'Detail Pengajuan',
    'subtitle' => 'Pratinjau Informasi Permohonan Pengajuan Admin CDW Engineering',
    'active'   => 'pengajuan-semua',
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pengajuan/semua') ?>" class="text-decoration-none text-muted">Pengajuan</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Pengajuan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-file-alt text-primary me-2"></i> Rincian Informasi Pengajuan</h4>
            <small class="text-muted">Pratinjau nomor pengajuan, kategori, tanggal pelaksanaan, dan status persetujuan.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/pengajuan/semua') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <?php if (strtolower($p['status'] ?? 'menunggu') === 'menunggu'): ?>
                <a href="<?= base_url('admin/pengajuan/edit/'.$p['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                    <i class="fas fa-edit me-1.5"></i> Edit Pengajuan
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3.5 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4a148c, #7b1fa2);">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><i class="fas fa-paper-plane me-2"></i> <?= esc($p['judul_pengajuan']) ?></h5>
                        <small class="text-white-50"><i class="fas fa-hashtag me-1"></i> Nomor: <?= esc($p['nomor_pengajuan'] ?? 'PGJ-'.$p['id']) ?></small>
                    </div>
                    <?php
                        $st = strtolower($p['status'] ?? 'menunggu');
                        $badge = 'bg-warning text-dark';
                        if ($st === 'disetujui') $badge = 'bg-success text-white';
                        if ($st === 'ditolak') $badge = 'bg-danger text-white';
                    ?>
                    <span class="badge <?= $badge ?> rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= strtoupper(esc($p['status'] ?? 'Menunggu')) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Informasi Pemohon -->
                    <div class="p-3 bg-light rounded-4 border mb-4">
                        <div class="row g-3 text-sm">
                            <div class="col-md-4">
                                <span class="text-muted d-block text-xs">Pemohon / Karyawan:</span>
                                <strong class="text-dark"><?= esc($p['nama_lengkap'] ?? session()->get('name') ?? 'Admin Staff') ?></strong>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted d-block text-xs">Divisi & Jabatan:</span>
                                <strong class="text-dark"><?= esc($p['divisi'] ?? '-') ?> &bull; <?= esc($p['jabatan'] ?? '-') ?></strong>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted d-block text-xs">Persetujuan Oleh:</span>
                                <strong class="text-dark"><?= esc($p['disetujui_oleh'] ?: 'Belum Ditinjau Direktur') ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-tags me-1 text-primary"></i> Kategori Pengajuan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($p['kategori_pengajuan']) ?></h6>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-calendar-alt me-1 text-info"></i> Tanggal Pengajuan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= !empty($p['tanggal_pengajuan']) ? date('d M Y', strtotime($p['tanggal_pengajuan'])) : date('d M Y', strtotime($p['created_at'])) ?></h6>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-business-time me-1 text-success"></i> Periode Pelaksanaan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1">
                                    <?= date('d M Y', strtotime($p['tanggal_mulai'])) ?>
                                    <?php if($p['tanggal_mulai'] !== $p['tanggal_selesai']): ?>
                                        - <?= date('d M Y', strtotime($p['tanggal_selesai'])) ?>
                                    <?php else: ?>
                                        (1 Hari)
                                    <?php endif; ?>
                                </h6>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-sticky-note text-primary me-2"></i> Keterangan & Alasan Pengajuan</h6>
                        <div class="p-3 bg-light rounded-3 border text-dark text-sm" style="line-height: 1.6; white-space: pre-line;">
                            <?= esc($p['keterangan'] ?: 'Tidak ada keterangan tambahan.') ?>
                        </div>
                    </div>

                    <!-- Lampiran Bukti Foto -->
                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-image text-primary me-2"></i> Lampiran Bukti Foto Pendukung</h6>
                        <?php if (!empty($p['bukti_foto'])): ?>
                            <div class="p-3 bg-light rounded-3 border text-center">
                                <a href="<?= base_url($p['bukti_foto']) ?>" target="_blank" title="Klik untuk membuka ukuran penuh">
                                    <img src="<?= base_url($p['bukti_foto']) ?>" alt="Bukti Foto Pengajuan" class="img-fluid rounded-3 shadow-sm border" style="max-height: 400px; object-fit: contain;">
                                </a>
                                <div class="mt-2 text-muted text-xs">
                                    <i class="fas fa-search-plus me-1"></i> Klik gambar untuk membuka ukuran asli penuh
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-3 bg-light rounded-3 border text-muted text-center text-xs">
                                <i class="fas fa-exclamation-circle me-1 text-warning"></i> Tidak ada lampiran gambar foto bukti pendukung.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('admin/pengajuan/semua') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <?php if (strtolower($p['status'] ?? 'menunggu') === 'menunggu'): ?>
                        <a href="<?= base_url('admin/pengajuan/edit/'.$p['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-edit me-1.5"></i> Edit Pengajuan Ini
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
