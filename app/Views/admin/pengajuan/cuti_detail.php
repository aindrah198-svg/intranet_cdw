<?php
$data = [
    'title'    => $title ?? 'Detail Pengajuan Cuti',
    'subtitle' => 'Pratinjau Lengkap Data Permohonan Cuti Karyawan',
    'active'   => 'pengajuan-cuti',
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pengajuan/cuti') ?>" class="text-decoration-none text-muted">Pengajuan Cuti</a></li>
                    <li class="breadcrumb-item active text-info fw-bold" aria-current="page">Detail Cuti</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-umbrella-beach text-info me-2"></i> Rincian Pengajuan Cuti</h4>
            <small class="text-muted">Pratinjau detail permohonan cuti, sisa kuota, dan tanggapan/approval manajemen.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/pengajuan/cuti') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <?php if (strtolower($c['status'] ?? 'menunggu') === 'menunggu'): ?>
                <a href="<?= base_url('admin/pengajuan/cuti/edit/'.$c['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                    <i class="fas fa-edit me-1.5"></i> Edit Cuti
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3.5 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0288d1, #01579b);">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><i class="fas fa-umbrella-beach me-2"></i> Permohonan Cuti (<?= esc($c['jenis_cuti']) ?>)</h5>
                        <small class="text-white-50"><i class="fas fa-hashtag me-1"></i> Nomor: <?= esc($c['nomor_cuti']) ?></small>
                    </div>
                    <?php
                        $st = strtolower($c['status'] ?? 'menunggu');
                        $badge = 'bg-warning text-dark';
                        if ($st === 'disetujui') $badge = 'bg-success text-white';
                        if ($st === 'ditolak') $badge = 'bg-danger text-white';
                    ?>
                    <span class="badge <?= $badge ?> rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= strtoupper(esc($c['status'] ?? 'Menunggu')) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-user me-1 text-primary"></i> Pemohon / Karyawan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($c['nama_lengkap'] ?: 'Admin User') ?></h6>
                                <small class="text-muted text-xs d-block"><?= esc($c['divisi'] ?: 'Administrasi') ?></small>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-calendar-alt me-1 text-info"></i> Tanggal Pengajuan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= date('d M Y', strtotime($c['tanggal_pengajuan'])) ?></h6>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-clock me-1 text-success"></i> Durasi Cuti</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= (int)$c['lama_hari'] ?> Hari Kerja</h6>
                                <small class="text-muted text-xs d-block"><?= date('d M Y', strtotime($c['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($c['tanggal_selesai'])) ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-align-left text-info me-2"></i> Alasan Permohonan Cuti</h6>
                        <div class="p-3 bg-light rounded-3 border text-dark text-sm" style="line-height: 1.6;">
                            <?= nl2br(esc($c['alasan'] ?: 'Tidak ada alasan tertulis.')) ?>
                        </div>
                    </div>

                    <?php if (!empty($c['disetujui_oleh']) || !empty($c['alasan_penolakan'])): ?>
                    <div class="p-3 rounded-3 border bg-info bg-opacity-10">
                        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-check-circle text-info me-2"></i> Informasi Approval Direktur / Manajemen</h6>
                        <small class="text-muted d-block mb-2">Ditinjau oleh: <strong><?= esc($c['disetujui_oleh'] ?: 'Direktur Utama') ?></strong> pada <?= !empty($c['disetujui_at']) ? date('d M Y H:i', strtotime($c['disetujui_at'])) : '-' ?></small>
                        <?php if (!empty($c['alasan_penolakan'])): ?>
                            <div class="alert alert-danger mb-0 py-2 text-xs">
                                <strong>Catatan Penolakan:</strong> <?= esc($c['alasan_penolakan']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('admin/pengajuan/cuti') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <?php if (strtolower($c['status'] ?? 'menunggu') === 'menunggu'): ?>
                        <a href="<?= base_url('admin/pengajuan/cuti/edit/'.$c['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-edit me-1.5"></i> Edit Cuti Ini
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
