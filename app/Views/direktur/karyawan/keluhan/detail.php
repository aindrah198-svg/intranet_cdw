<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<?php
    $status = $keluhan['status'] ?? 'baru';
    $statusPillClass = 'status-pill-baru';
    $statusIcon = 'fas fa-exclamation-circle';

    if ($status === 'diproses') {
        $statusPillClass = 'status-pill-diproses';
        $statusIcon = 'fas fa-spinner';
    } elseif ($status === 'selesai') {
        $statusPillClass = 'status-pill-selesai';
        $statusIcon = 'fas fa-check-circle';
    } elseif ($status === 'ditolak') {
        $statusPillClass = 'status-pill-ditolak';
        $statusIcon = 'fas fa-times-circle';
    }

    $statusLabel = match($status) {
        'baru'     => 'Belum Tanggap',
        'diproses' => 'Sedang Diproses',
        'selesai'  => 'Selesai',
        'ditolak'  => 'Ditolak',
        default    => ucfirst($status),
    };

    $nama = $keluhan['nama_lengkap'] ?? 'Karyawan';
    $initial = !empty($nama) ? strtoupper(substr($nama, 0, 1)) : 'K';
    $keluhanIdTag = 'KLH' . str_pad($keluhan['id'], 3, '0', STR_PAD_LEFT);
?>

<style>
    /* Styling Premium Modern Material & Glassmorphism (Sama Persis dengan Detail Karyawan) */
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
    
    .status-pill-baru {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }
    
    .status-pill-diproses {
        background: rgba(13, 202, 240, 0.12);
        color: #0dcaf0;
        border: 1px solid rgba(13, 202, 240, 0.25);
    }

    .status-pill-selesai {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .status-pill-ditolak {
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

    .form-control-custom, .form-select-custom {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.12);
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

    .quick-chip {
        font-size: 0.73rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-block;
        margin-bottom: 4px;
    }
    .quick-chip:hover {
        background: #1e3c72;
        color: #ffffff;
        border-color: #1e3c72;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- Header Section Terpadu -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-comment-alt fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Detail Keluhan Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Rincian keluhan dari <strong><?= esc($nama) ?></strong> dan form tindak lanjut.</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/karyawan/keluhan') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> <span class="d-none d-md-inline">Kembali ke Daftar</span><span class="d-inline d-md-none">Kembali</span>
            </a>
        </div>
    </div>

    <!-- Alert Flash Data -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show text-white shadow-sm border-0 mb-4" role="alert" style="border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show text-white shadow-sm border-0 mb-4" role="alert" style="border-radius: 12px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Main Detail Grid Layout -->
    <div class="row g-4 mb-5">

        <!-- Sidebar Profil Karyawan & Form Tanggapan -->
        <div class="col-12 col-lg-4">
            
            <!-- Profil Kartu Karyawan -->
            <div class="card employee-card-modern p-4 text-center mb-4">
                <div class="d-flex flex-column align-items-center justify-content-center py-2">
                    <div class="avatar-glow-lg text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mb-3">
                        <?= $initial ?>
                    </div>
                    <h4 class="fw-bold text-dark mb-1"><?= esc($nama) ?></h4>
                    <div class="d-flex align-items-center gap-2 my-2">
                        <span class="id-tag">ID: <?= $keluhanIdTag ?></span>
                        <span class="status-pill <?= $statusPillClass ?>">
                            <i class="<?= $statusIcon ?> me-1"></i> <?= $statusLabel ?>
                        </span>
                    </div>
                    <p class="text-muted text-sm mb-3"><i class="fas fa-briefcase me-1 text-primary"></i> <?= esc($keluhan['divisi'] ?: 'Staf') ?> | <?= esc($keluhan['jabatan'] ?: 'Karyawan') ?></p>
                </div>

                <div class="pt-3 border-top border-light text-start">
                    <div class="data-pill-bar mb-2">
                        <div class="data-label"><i class="far fa-id-card text-primary"></i> NIK Karyawan</div>
                        <div class="data-value"><?= esc($keluhan['nik'] ?: '-') ?></div>
                    </div>
                    <div class="data-pill-bar mb-2">
                        <div class="data-label"><i class="far fa-calendar-alt text-primary"></i> Tanggal Lapor</div>
                        <div class="data-value"><?= date('d F Y', strtotime($keluhan['tanggal'])) ?></div>
                    </div>
                    <div class="data-pill-bar">
                        <div class="data-label"><i class="fas fa-tags text-primary"></i> Kategori Keluhan</div>
                        <div class="data-value"><?= esc($keluhan['kategori']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Form Tanggapan Direktur -->
            <div class="card employee-card-modern p-4">
                <div class="form-section-title mb-3">
                    <i class="fas fa-reply text-primary"></i> Form Tanggapan Direktur
                </div>

                <form action="<?= base_url('direktur/karyawan/keluhan/tanggapi/'.$keluhan['id']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-sm">Update Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-select-custom" required>
                            <option value="diproses" <?= $keluhan['status']=='diproses' ? 'selected' : '' ?>>🔵 Sedang Diproses (Follow Up)</option>
                            <option value="selesai" <?= $keluhan['status']=='selesai' ? 'selected' : '' ?>>🟢 Selesai / Solved</option>
                            <option value="ditolak" <?= $keluhan['status']=='ditolak' ? 'selected' : '' ?>>🔴 Ditolak / Tidak Valid</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-sm">Isi Tanggapan / Instruksi <span class="text-danger">*</span></label>
                        
                        <!-- Quick Response Chips -->
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <span class="quick-chip" onclick="insertTemplate('Keluhan ini telah ditinjau dan sedang ditindaklanjuti oleh HRD.')">
                                + Follow up HRD
                            </span>
                            <span class="quick-chip" onclick="insertTemplate('Masalah telah diselesaikan secara bersama. Terima kasih atas laporannya.')">
                                + Solved
                            </span>
                            <span class="quick-chip" onclick="insertTemplate('Silakan dijadwalkan pertemuan langsung dengan Manajemen.')">
                                + Agendakan Meeting
                            </span>
                        </div>

                        <textarea name="tanggapan" id="tanggapanInput" class="form-control form-control-custom" rows="4" placeholder="Ketikkan arahan atau jawaban Anda..." required><?= esc($keluhan['tanggapan'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-2.5 text-sm fw-semibold shadow-sm">
                        <i class="fas fa-paper-plane me-1.5"></i> Simpan Tanggapan
                    </button>
                </form>
            </div>

        </div>

        <!-- Grid Bilah Data Informasi Keluhan Lengkap -->
        <div class="col-12 col-lg-8">
            
            <!-- Detail Rincian Keluhan -->
            <div class="card employee-card-modern p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                    <div>
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2 font-weight-normal" style="font-size: 0.78rem;">
                            <?= esc($keluhan['kategori']) ?>
                        </span>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 1.35rem; letter-spacing: -0.2px;">
                            <?= esc($keluhan['judul']) ?>
                        </h3>
                    </div>
                    <span class="text-xs text-muted fw-semibold">
                        <i class="far fa-clock me-1"></i> Dilaporkan pada <?= date('d M Y, H:i', strtotime($keluhan['created_at'] ?? $keluhan['tanggal'])) ?> WIB
                    </span>
                </div>

                <div class="py-3">
                    <div class="form-section-title mb-2">
                        <i class="fas fa-align-left text-primary"></i> Rincian Keluhan Lengkap
                    </div>
                    <div class="p-3.5 rounded-3 text-dark bg-light border border-light" style="line-height: 1.8; white-space: pre-line; font-size: 0.95rem;">
                        <?= esc($keluhan['deskripsi']) ?>
                    </div>
                </div>
            </div>

            <!-- Card Tanggapan Direktur yang Sudah Ada -->
            <?php if(!empty($keluhan['tanggapan'])): ?>
                <div class="card employee-card-modern p-4" style="border-left: 5px solid #198754 !important;">
                    <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Tanggapan Direktur / Manajemen</h5>
                                <small class="text-muted">
                                    Ditanggapi oleh: <strong><?= esc($keluhan['nama_penanggap'] ?? 'Direktur') ?></strong> 
                                    <?= !empty($keluhan['tanggal_tanggapan']) ? '• pada ' . date('d M Y, H:i', strtotime($keluhan['tanggal_tanggapan'])) . ' WIB' : '' ?>
                                </small>
                            </div>
                        </div>

                        <span class="status-pill status-pill-selesai">
                            <i class="fas fa-check-circle me-1"></i> Sudah Ditanggapi
                        </span>
                    </div>

                    <div class="p-3.5 rounded-3 text-dark bg-success-subtle border border-success-subtle" style="line-height: 1.8; white-space: pre-line; font-size: 0.95rem;">
                        <?= esc($keluhan['tanggapan']) ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card employee-card-modern p-4 text-center py-5">
                    <div class="avatar-glow mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.4rem; background: rgba(255, 193, 7, 0.15); color: #b58100; border: 1px solid rgba(255, 193, 7, 0.3);">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Tanggapan</h5>
                    <p class="text-muted small mb-0" style="max-width: 420px; margin: 0 auto;">
                        Keluhan ini belum mendapatkan tanggapan resmi dari Direktur. Gunakan formulir di sebelah kiri untuk memberikan arahan atau solusi.
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    function insertTemplate(text) {
        const input = document.getElementById('tanggapanInput');
        if (input.value.trim() === '') {
            input.value = text;
        } else {
            input.value = input.value + '\n\n' + text;
        }
        input.focus();
    }
</script>

<?= $this->include('direktur/templates/footer') ?>
