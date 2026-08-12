<?php
$data = [
    'title'     => $title ?? 'Detail Penugasan Kendaraan',
    'subtitle'  => 'Rincian Lengkap Operasional Mobil Dinas CDW Engineering',
    'active'    => 'kendaraan',
    'user'      => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/fasilitas/kendaraan') ?>" class="text-decoration-none text-muted">Koordinasi Kendaraan</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Kendaraan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-car-side text-primary me-2"></i> Rincian Operasional Kendaraan Dinas</h4>
            <small class="text-muted">Pratinjau lengkap armada, driver, pengguna, rute tujuan, dan estimasi waktu operasional.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/fasilitas/kendaraan') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('admin/fasilitas/kendaraan/edit/'.$k['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Operasional
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3.5 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1565c0, #1e88e5);">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><i class="fas fa-car me-2"></i> <?= esc($k['nama_kendaraan']) ?></h5>
                        <small class="text-white-50"><i class="fas fa-id-card me-1"></i> Plat Nomor: <?= esc($k['plat_nomor'] ?: '-') ?></small>
                    </div>
                    <?php
                        $st = strtolower($k['status'] ?? 'sedang berjalan');
                        $badge = 'bg-primary text-white';
                        if ($st === 'disetujui') $badge = 'bg-success text-white';
                        if ($st === 'pending') $badge = 'bg-warning text-dark';
                        if ($st === 'selesai') $badge = 'bg-secondary text-white';
                        if ($st === 'ditolak') $badge = 'bg-danger text-white';
                    ?>
                    <span class="badge <?= $badge ?> rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= strtoupper(esc($k['status'])) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-user-tie me-1 text-primary"></i> Pengemudi / Driver</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($k['driver'] ?: 'Tanpa Driver (Self Drive)') ?></h6>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-users me-1 text-info"></i> Tim Pemakai / Pengguna</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($k['pengguna']) ?></h6>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-map-marker-alt me-1 text-danger"></i> Tujuan / Rute Kunjungan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($k['tujuan']) ?></h6>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-4">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-plane-departure me-1 text-primary fs-6"></i> Waktu Keberangkatan</small>
                                <h6 class="fw-bold text-primary mb-0"><?= !empty($k['tanggal_mulai']) ? date('d M Y, H:i', strtotime($k['tanggal_mulai'])) . ' WIB' : '-' ?></h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-4">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-plane-arrival me-1 text-info fs-6"></i> Estimasi Kepulangan</small>
                                <h6 class="fw-bold text-info mb-0"><?= !empty($k['tanggal_selesai']) ? date('d M Y, H:i', strtotime($k['tanggal_selesai'])) . ' WIB' : '-' ?></h6>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-sticky-note text-primary me-2"></i> Catatan Khusus & Equipments Bawaan</h6>
                        <div class="p-3 bg-light rounded-3 border text-dark text-sm" style="line-height: 1.6;">
                            <?= nl2br(esc($k['catatan'] ?: 'Tidak ada catatan khusus.')) ?>
                        </div>
                    </div>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('admin/fasilitas/kendaraan') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <a href="<?= base_url('admin/fasilitas/kendaraan/edit/'.$k['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-edit me-1.5"></i> Edit Penugasan Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
