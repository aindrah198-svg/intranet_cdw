<?php
$data = [
    'title'    => $title ?? 'Detail Booking Ruang Meeting',
    'subtitle' => 'Rincian Lengkap Reservasi Ruang Rapat CDW Engineering',
    'active'   => 'booking-ruang',
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/fasilitas/booking-ruang') ?>" class="text-decoration-none text-muted">Booking Ruang</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Reservasi</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-calendar-check text-success me-2"></i> Rincian Reservasi Ruang Rapat</h4>
            <small class="text-muted">Pratinjau lengkap agenda, peminjam, waktu penggunaan, dan status persetujuan.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/fasilitas/booking-ruang') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('admin/fasilitas/booking-ruang/edit/'.$b['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Reservasi
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3.5 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #2e7d32, #4caf50);">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><i class="fas fa-door-open me-2"></i> <?= esc($b['nama_ruangan']) ?></h5>
                        <small class="text-white-50"><i class="fas fa-user me-1"></i> Peminjam: <?= esc($b['peminjam']) ?> (<?= esc($b['divisi'] ?: 'General') ?>)</small>
                    </div>
                    <?php
                        $st = strtolower($b['status'] ?? 'disetujui');
                        $badge = 'bg-success text-white';
                        if ($st === 'pending') $badge = 'bg-warning text-dark';
                        if ($st === 'ditolak') $badge = 'bg-danger text-white';
                        if ($st === 'selesai') $badge = 'bg-secondary text-white';
                    ?>
                    <span class="badge <?= $badge ?> rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= strtoupper(esc($b['status'])) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-calendar-day me-1 text-primary"></i> Tanggal Rapat</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= date('d M Y', strtotime($b['tanggal'])) ?></h6>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-clock me-1 text-info"></i> Jam Penggunaan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= date('H:i', strtotime($b['jam_mulai'])) ?> - <?= date('H:i', strtotime($b['jam_selesai'])) ?> WIB</h6>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-users me-1 text-success"></i> Jumlah Peserta</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= (int)$b['jumlah_peserta'] ?> Orang</h6>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-sticky-note text-primary me-2"></i> Agenda / Topik Rapat</h6>
                        <div class="p-3 bg-light rounded-3 border text-dark text-sm" style="line-height: 1.6;">
                            <?= nl2br(esc($b['agenda'] ?: 'Tidak ada agenda tertulis.')) ?>
                        </div>
                    </div>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                    <a href="<?= base_url('admin/fasilitas/booking-ruang') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold me-2">Kembali</a>
                    <a href="<?= base_url('admin/fasilitas/booking-ruang/edit/'.$b['id']) ?>" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-edit me-1.5"></i> Edit Reservasi Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
