<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<?php
$title = $title ?? 'Detail Absensi';
$absensi = $absensi ?? [];

$nama = $absensi['nama_lengkap'] ?? $absensi['nama_panggilan'] ?? 'Karyawan';
$initial = strtoupper(substr($nama, 0, 1));
$st = $absensi['status'] ?? 'Hadir';

$statusPillClass = match($st) {
    'Hadir'     => 'status-pill-hadir',
    'Terlambat' => 'status-pill-terlambat',
    'Izin'      => 'status-pill-izin',
    'Sakit'     => 'status-pill-sakit',
    'Alpha'     => 'status-pill-alpha',
    default     => 'status-pill-hadir'
};
$statusIcon = match($st) {
    'Hadir'     => 'fas fa-check-circle',
    'Terlambat' => 'fas fa-clock',
    'Izin'      => 'fas fa-info-circle',
    'Sakit'     => 'fas fa-user-md',
    'Alpha'     => 'fas fa-times-circle',
    default     => 'fas fa-check-circle'
};
?>

<style>
    /* Styling Premium Modern Material & Glassmorphism */
    .employee-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
    }

    .avatar-glow-lg {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.35);
        border: 3px solid rgba(255, 255, 255, 0.9);
        width: 80px;
        height: 80px;
        font-size: 2.2rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 14px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    .status-pill-hadir {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }
    
    .status-pill-terlambat {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-izin {
        background: rgba(13, 202, 240, 0.12);
        color: #0dcaf0;
        border: 1px solid rgba(13, 202, 240, 0.25);
    }

    .status-pill-sakit {
        background: rgba(111, 66, 193, 0.12);
        color: #6f42c1;
        border: 1px solid rgba(111, 66, 193, 0.25);
    }

    .status-pill-alpha {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    .data-pill-bar {
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.2s ease;
        height: 100%;
    }
    
    .data-pill-bar:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .data-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .data-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #0f172a;
    }

    .id-tag {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }

    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e3c72;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 16px;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- Header Section Terpadu -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-id-card-alt fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Detail Record Absensi</h4>
                <small class="text-muted d-none d-sm-inline">Rincian log kehadiran dan aktivitas absensi karyawan.</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/monitoring/absensi') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> <span class="d-none d-md-inline">Kembali ke Daftar</span><span class="d-inline d-md-none">Kembali</span>
            </a>
        </div>
    </div>

    <?php if (empty($absensi)): ?>
        <div class="alert alert-danger rounded-3 shadow-sm p-4">Data absensi tidak ditemukan.</div>
    <?php else: ?>

    <div class="row g-4 mb-5">
        <!-- Sidebar Kartu Karyawan -->
        <div class="col-12 col-lg-4">
            <div class="card employee-card-modern p-4 text-center h-100">
                <div class="d-flex flex-column align-items-center justify-content-center py-2">
                    <div class="avatar-glow-lg text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mb-3">
                        <?= $initial ?>
                    </div>
                    <h4 class="fw-bold text-dark mb-1"><?= esc($nama) ?></h4>
                    <div class="d-flex align-items-center gap-2 my-2">
                        <span class="id-tag">NIK: <?= esc($absensi['nik'] ?: '-') ?></span>
                        <span class="status-pill <?= $statusPillClass ?>">
                            <i class="<?= $statusIcon ?> me-1"></i> <?= esc($st) ?>
                        </span>
                    </div>
                    <p class="text-muted text-sm mb-3"><i class="fas fa-briefcase me-1 text-primary"></i> <?= esc($absensi['jabatan'] ?: 'Staf') ?> | <?= esc($absensi['departemen'] ?: 'Umum') ?></p>
                </div>

                <div class="pt-3 border-top border-light text-start">
                    <div class="data-pill-bar mb-2">
                        <div class="data-label"><i class="far fa-calendar-alt text-primary"></i> Tanggal Record</div>
                        <div class="data-value"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></div>
                    </div>
                    <div class="data-pill-bar">
                        <div class="data-label"><i class="fas fa-clock text-primary"></i> Shift Kerja</div>
                        <div class="data-value">Shift <?= ucfirst(esc($absensi['shift'] ?? 'siang')) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Kehadiran Lengkap -->
        <div class="col-12 col-lg-8">
            <div class="card employee-card-modern p-4 h-100">
                <div class="form-section-title">
                    <i class="fas fa-info-circle text-primary"></i> Rincian Kehadiran Karyawan
                </div>

                <!-- Box Masuk & Pulang -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="p-3.5 rounded-3 border" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-xs fw-bold text-uppercase text-success"><i class="fas fa-sign-in-alt me-1"></i> Jam Masuk</span>
                                <span class="badge bg-success text-white rounded-pill px-2.5 py-1">Check In</span>
                            </div>
                            <h2 class="fw-bold text-success mb-1"><?= !empty($absensi['waktu_masuk']) ? date('H:i', strtotime($absensi['waktu_masuk'])).' WIB' : '-' ?></h2>
                            <?php if(!empty($absensi['lokasi_masuk'])): ?>
                                <p class="text-xs text-muted mb-0 mt-2"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc($absensi['lokasi_masuk']) ?></p>
                            <?php endif; ?>
                            <?php if(!empty($absensi['latitude_masuk']) && !empty($absensi['longitude_masuk'])): ?>
                                <a href="https://maps.google.com/?q=<?= $absensi['latitude_masuk'] ?>,<?= $absensi['longitude_masuk'] ?>" target="_blank" class="btn btn-sm btn-link text-primary p-0 text-decoration-none text-xs mt-1">
                                    <i class="fas fa-external-link-alt me-1"></i> Lihat Peta Google Maps
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="p-3.5 rounded-3 border" style="background: #fff1f2; border-color: #fecdd3 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-xs fw-bold text-uppercase text-danger"><i class="fas fa-sign-out-alt me-1"></i> Jam Pulang</span>
                                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1">Check Out</span>
                            </div>
                            <h2 class="fw-bold text-danger mb-1"><?= !empty($absensi['waktu_pulang']) ? date('H:i', strtotime($absensi['waktu_pulang'])).' WIB' : '-' ?></h2>
                            <?php if(!empty($absensi['lokasi_pulang'])): ?>
                                <p class="text-xs text-muted mb-0 mt-2"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc($absensi['lokasi_pulang']) ?></p>
                            <?php endif; ?>
                            <?php if(!empty($absensi['latitude_pulang']) && !empty($absensi['longitude_pulang'])): ?>
                                <a href="https://maps.google.com/?q=<?= $absensi['latitude_pulang'] ?>,<?= $absensi['longitude_pulang'] ?>" target="_blank" class="btn btn-sm btn-link text-primary p-0 text-decoration-none text-xs mt-1">
                                    <i class="fas fa-external-link-alt me-1"></i> Lihat Peta Google Maps
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Grid Data Bilah Informasi Tambahan -->
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="data-pill-bar">
                            <div class="data-label"><i class="fas fa-hourglass-half text-primary"></i> Total Jam Kerja</div>
                            <div class="data-value"><?= !empty($absensi['jam_kerja']) ? number_format($absensi['jam_kerja'], 1).' jam' : '-' ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="data-pill-bar">
                            <div class="data-label"><i class="fas fa-history text-primary"></i> Keterlambatan</div>
                            <div class="data-value">
                                <?php if(($absensi['terlambat'] ?? 0) > 0): ?>
                                    <span class="text-warning fw-bold"><?= $absensi['terlambat'] ?> menit</span>
                                <?php else: ?>
                                    <span class="text-success fw-bold">Tepat Waktu</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="data-pill-bar">
                            <div class="data-label"><i class="fas fa-user-clock text-primary"></i> Jam Lembur</div>
                            <div class="data-value"><?= !empty($absensi['jam_lembur']) ? number_format($absensi['jam_lembur'], 1).' jam' : '0 jam' ?></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="data-pill-bar">
                            <div class="data-label"><i class="fas fa-comment-alt text-primary"></i> Keterangan / Alasan</div>
                            <div class="data-value" style="white-space: pre-line;">
                                <?= !empty($absensi['keterangan']) ? esc($absensi['keterangan']) : 'Tidak ada keterangan khusus.' ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<?= $this->include('direktur/templates/footer') ?>
