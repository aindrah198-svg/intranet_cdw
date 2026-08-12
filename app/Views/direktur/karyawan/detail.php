<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<?php
    $status = $karyawan['status_karyawan'] ?? '';
    $statusPillClass = 'status-pill-default';
    $statusIcon = 'fas fa-info-circle';

    if ($status === 'Tetap') {
        $statusPillClass = 'status-pill-tetap';
        $statusIcon = 'fas fa-check-circle';
    } elseif ($status === 'Kontrak') {
        $statusPillClass = 'status-pill-kontrak';
        $statusIcon = 'fas fa-clock';
    } elseif ($status === 'Probation') {
        $statusPillClass = 'status-pill-probation';
        $statusIcon = 'fas fa-user-clock';
    } elseif ($status === 'Staff') {
        $statusPillClass = 'status-pill-staff';
        $statusIcon = 'fas fa-user';
    }
    
    $initial = !empty($karyawan['nama_lengkap']) ? strtoupper(substr($karyawan['nama_lengkap'], 0, 1)) : 'K';
    $employeeIdTag = 'E' . str_pad($karyawan['id'], 3, '0', STR_PAD_LEFT);
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
    
    .status-pill-tetap {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }
    
    .status-pill-kontrak {
        background: rgba(13, 202, 240, 0.12);
        color: #0dcaf0;
        border: 1px solid rgba(13, 202, 240, 0.25);
    }

    .status-pill-probation {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-staff {
        background: rgba(13, 110, 253, 0.12);
        color: #0d6efd;
        border: 1px solid rgba(13, 110, 253, 0.25);
    }

    .status-pill-default {
        background: rgba(108, 117, 125, 0.12);
        color: #6c757d;
        border: 1px solid rgba(108, 117, 125, 0.25);
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
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- Header Section Terpadu (Selaras dengan Halaman Kelola Karyawan) -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-id-card-alt fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Detail Data Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Informasi komprehensif profil dan status pekerjaan karyawan.</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/karyawan') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> <span class="d-none d-md-inline">Kembali</span>
            </a>
            <a href="<?= base_url('direktur/karyawan/edit/'.$karyawan['id']) ?>" class="btn btn-info text-white rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-user-edit me-1.5"></i> <span class="d-none d-md-inline">Edit Data</span><span class="d-inline d-md-none">Edit</span>
            </a>
        </div>
    </div>

    <!-- Main Detail Grid Layout -->
    <div class="row g-4 mb-5">
        <!-- Sidebar Kartu Profil Karyawan -->
        <div class="col-12 col-lg-4">
            <div class="card employee-card-modern p-4 text-center h-100">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <div class="avatar-glow-lg text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mb-3">
                        <?= $initial ?>
                    </div>
                    <h4 class="fw-bold text-dark mb-1"><?= esc($karyawan['nama_lengkap']) ?></h4>
                    <div class="d-flex align-items-center gap-2 my-2">
                        <span class="id-tag">ID: <?= $employeeIdTag ?></span>
                        <span class="status-pill <?= $statusPillClass ?>">
                            <i class="<?= $statusIcon ?> me-1"></i> <?= esc($status) ?>
                        </span>
                    </div>
                    <p class="text-muted text-sm mb-4"><i class="fas fa-briefcase me-1 text-primary"></i> <?= esc($karyawan['divisi']) ?> | <?= esc($karyawan['jabatan']) ?></p>

                    <div class="w-100 pt-3 border-top border-light d-flex justify-content-center gap-2">
                        <a href="<?= base_url('direktur/karyawan/edit/'.$karyawan['id']) ?>" class="btn btn-outline-primary rounded-pill px-4 py-2 text-sm fw-semibold w-100">
                            <i class="fas fa-edit me-1.5"></i> Edit Karyawan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Bilah Data Informasi Rinci -->
        <div class="col-12 col-lg-8">
            <div class="card employee-card-modern p-4 h-100">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i> Informasi Rinci Karyawan
                </h5>
                
                <div class="row g-3">
                    <!-- NIK -->
                    <div class="col-12 col-sm-6">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="far fa-id-card text-primary"></i> NIK (Nomor Induk Karyawan)
                            </div>
                            <div class="data-value">
                                <?= esc($karyawan['nik']) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-12 col-sm-6">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="far fa-envelope text-primary"></i> Email
                            </div>
                            <div class="data-value text-break">
                                <?= !empty($karyawan['email']) ? esc($karyawan['email']) : '-' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Divisi -->
                    <div class="col-12 col-sm-6">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="fas fa-sitemap text-primary"></i> Divisi
                            </div>
                            <div class="data-value">
                                <?= !empty($karyawan['divisi']) ? esc($karyawan['divisi']) : '-' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Jabatan -->
                    <div class="col-12 col-sm-6">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="fas fa-user-tag text-primary"></i> Jabatan
                            </div>
                            <div class="data-value">
                                <?= !empty($karyawan['jabatan']) ? esc($karyawan['jabatan']) : '-' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tempat, Tanggal Lahir -->
                    <div class="col-12 col-sm-6">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="far fa-calendar-alt text-primary"></i> Tempat, Tanggal Lahir
                            </div>
                            <div class="data-value">
                                <?= !empty($karyawan['tempat_lahir']) ? esc($karyawan['tempat_lahir']) : '-' ?>,
                                <?= !empty($karyawan['tanggal_lahir']) ? date('d M Y', strtotime($karyawan['tanggal_lahir'])) : '-' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="col-12 col-sm-6">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="fas fa-venus-mars text-primary"></i> Jenis Kelamin
                            </div>
                            <div class="data-value">
                                <?= $karyawan['jenis_kelamin'] == 'L' ? 'Laki-laki' : ($karyawan['jenis_kelamin'] == 'P' ? 'Perempuan' : '-') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Telepon -->
                    <div class="col-12 col-sm-6">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="fas fa-phone-alt text-primary"></i> No. Telepon / WhatsApp
                            </div>
                            <div class="data-value">
                                <?= !empty($karyawan['telepon']) ? esc($karyawan['telepon']) : '-' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Masuk -->
                    <div class="col-12 col-sm-6">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="far fa-clock text-primary"></i> Tanggal Masuk (Join Date)
                            </div>
                            <div class="data-value">
                                <?= !empty($karyawan['tanggal_masuk']) ? date('d M Y', strtotime($karyawan['tanggal_masuk'])) : '-' ?>
                            </div>
                        </div>
                    </div>

                    <!-- NPWP -->
                    <div class="col-12 col-sm-6">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="fas fa-file-invoice-dollar text-primary"></i> No. NPWP
                            </div>
                            <div class="data-value">
                                <?= !empty($karyawan['no_npwp']) ? esc($karyawan['no_npwp']) : '-' ?>
                            </div>
                        </div>
                    </div>

                    <!-- No. KTP / NIK Kependudukan -->
                    <div class="col-12 col-sm-6">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="far fa-address-card text-primary"></i> No. KTP / NIK Kependudukan
                            </div>
                            <div class="data-value">
                                <?= !empty($karyawan['no_ktp']) ? esc($karyawan['no_ktp']) : '-' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="col-12">
                        <div class="data-pill-bar">
                            <div class="data-label">
                                <i class="fas fa-map-marker-alt text-primary"></i> Alamat Lengkap
                            </div>
                            <div class="data-value">
                                <?= !empty($karyawan['alamat']) ? esc($karyawan['alamat']) : '-' ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->include('direktur/templates/footer') ?>
