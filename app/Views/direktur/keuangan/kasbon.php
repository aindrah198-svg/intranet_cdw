<?php
$title = $title ?? 'Persetujuan & Data Kasbon Karyawan';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'keuangan'
];
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>

<style>
    /* Styling Premium Modern Material & Glassmorphism */
    .kasbon-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .kasbon-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
    }

    .avatar-glow-kasbon {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 0 4px 14px rgba(30, 60, 114, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .status-pill-disetujui {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .status-pill-menunggu {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-ditolak {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    .status-pill-lunas {
        background: rgba(13, 110, 253, 0.12);
        color: #0d6efd;
        border: 1px solid rgba(13, 110, 253, 0.25);
    }

    .data-pill-bar {
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        padding: 10px 14px;
        transition: all 0.2s ease;
        height: 100%;
    }
    
    .data-pill-bar:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .data-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .data-value {
        font-size: 0.92rem;
        font-weight: 600;
        color: #0f172a;
    }

    .btn-action-pill {
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid transparent;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-action-view {
        background: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
        border-color: rgba(13, 110, 253, 0.2);
    }
    .btn-action-view:hover {
        background: #0d6efd;
        color: #ffffff;
    }

    .btn-action-approve {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border-color: rgba(25, 135, 84, 0.25);
    }
    .btn-action-approve:hover {
        background: #198754;
        color: #ffffff;
    }

    .btn-action-reject {
        background: rgba(220, 53, 69, 0.08);
        color: #dc3545;
        border-color: rgba(220, 53, 69, 0.2);
    }
    .btn-action-reject:hover {
        background: #dc3545;
        color: #ffffff;
    }

    .btn-action-edit {
        background: rgba(255, 193, 7, 0.12);
        color: #b58100;
        border-color: rgba(255, 193, 7, 0.3);
    }
    .btn-action-edit:hover {
        background: #ffc107;
        color: #000000;
    }

    .id-tag {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }

    .stat-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
        transition: transform 0.2s ease;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-hand-holding-usd fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Data & Persetujuan Kasbon Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Kelola pengajuan kasbon, sisa pinjaman, dan pemotongan otomatis lewat penggajian.</small>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahKasbon">
                <i class="fas fa-plus me-1.5"></i> <span>Tambah Pengajuan Kasbon</span>
            </button>
            <a href="<?= base_url('direktur/keuangan/kasbon/export-excel'.($filterStatus ? '?status='.$filterStatus : '')) ?>" class="btn btn-outline-success rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-file-excel me-1.5"></i> <span class="d-none d-sm-inline">Export Excel</span>
            </a>
            <a href="<?= base_url('direktur/keuangan/kasbon/cetak'.($filterStatus ? '?status='.$filterStatus : '')) ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-file-pdf me-1.5"></i> <span class="d-none d-sm-inline">Export PDF</span>
            </a>
        </div>
    </div>

    <!-- Flash Notifications -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 2. Ringkasan Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card stat-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-wallet fa-lg text-primary"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Total Pinjaman</small>
                        <h5 class="fw-bold mb-0 text-dark">Rp <?= number_format($totalPinjaman, 0, ',', '.') ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card stat-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-balance-scale fa-lg text-warning"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Total Sisa Pinjaman</small>
                        <h5 class="fw-bold mb-0 text-warning">Rp <?= number_format($totalSisa, 0, ',', '.') ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card stat-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-clock fa-lg text-info"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Menunggu Approval</small>
                        <h5 class="fw-bold mb-0 text-info"><?= $totalPendingCount ?> Pengajuan</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card stat-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-check-double fa-lg text-success"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Kasbon Lunas</small>
                        <h5 class="fw-bold mb-0 text-success"><?= $totalLunasCount ?> Kasbon</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Filter Navigation Tabs & Search -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="btn-group bg-white p-1 rounded-pill shadow-sm border" role="group">
            <a href="<?= base_url('direktur/keuangan/kasbon') ?>" class="btn btn-sm rounded-pill px-3 fw-semibold <?= empty($filterStatus) ? 'btn-primary' : 'btn-light text-muted' ?>">Semua</a>
            <a href="<?= base_url('direktur/keuangan/kasbon?status=pending') ?>" class="btn btn-sm rounded-pill px-3 fw-semibold <?= $filterStatus === 'pending' ? 'btn-primary' : 'btn-light text-muted' ?>">Menunggu</a>
            <a href="<?= base_url('direktur/keuangan/kasbon?status=approved') ?>" class="btn btn-sm rounded-pill px-3 fw-semibold <?= $filterStatus === 'approved' ? 'btn-primary' : 'btn-light text-muted' ?>">Disetujui</a>
            <a href="<?= base_url('direktur/keuangan/kasbon?status=lunas') ?>" class="btn btn-sm rounded-pill px-3 fw-semibold <?= $filterStatus === 'lunas' ? 'btn-primary' : 'btn-light text-muted' ?>">Lunas</a>
            <a href="<?= base_url('direktur/keuangan/kasbon?status=rejected') ?>" class="btn btn-sm rounded-pill px-3 fw-semibold <?= $filterStatus === 'rejected' ? 'btn-primary' : 'btn-light text-muted' ?>">Ditolak</a>
        </div>
        <div class="position-relative" style="min-width: 250px;">
            <input type="text" id="searchKasbon" class="form-control form-control-sm rounded-pill ps-4 py-2" placeholder="Cari karyawan, nomor kasbon...">
        </div>
    </div>

    <!-- Modal Tambah Kasbon -->
    <div class="modal fade" id="modalTambahKasbon" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-plus-circle text-primary me-2"></i> Tambah Pengajuan Kasbon
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('direktur/keuangan/kasbon/simpan') ?>" method="POST">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Pilih Karyawan *</label>
                                <select name="karyawan_id" class="form-select rounded-3" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    <?php foreach($karyawanList as $kar): ?>
                                        <option value="<?= $kar['id'] ?>"><?= esc($kar['nama_lengkap']) ?> (NIK: <?= esc($kar['nik']) ?> - <?= esc($kar['jabatan']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Jumlah Kasbon (Rp) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                    <input type="text" name="jumlah_kasbon" class="form-control rounded-end input-rupiah" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Tanggal Dibutuhkan</label>
                                <input type="date" name="tanggal_dibutuhkan" class="form-control rounded-3" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Rencana Pelunasan</label>
                                <input type="text" name="rencana_pelunasan" class="form-control rounded-3" placeholder="Contoh: Potong gaji bulanan">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm">Alasan Pengajuan Kasbon *</label>
                                <textarea name="alasan" class="form-control rounded-3" rows="3" placeholder="Alasan kebutuhan kasbon..." required></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Status Persetujuan Direktur</label>
                                <select name="status_direktur" class="form-select rounded-3">
                                    <option value="Disetujui">Langsung Disetujui</option>
                                    <option value="Menunggu">Menunggu Review</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Simpan Kasbon</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 4. Kasbon Cards Grid List -->
    <div class="row g-3" id="kasbonCardContainer">
        <?php if(empty($kasbon)): ?>
            <div class="col-12 text-center py-5 bg-white rounded-3 shadow-sm border border-light">
                <i class="fas fa-wallet fa-3x text-muted mb-3 opacity-50"></i>
                <p class="text-muted fw-semibold mb-2">Belum ada pengajuan kasbon karyawan yang ditemukan.</p>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold text-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKasbon">
                    <i class="fas fa-plus me-1"></i> Buat Kasbon Karyawan Baru
                </button>
            </div>
        <?php else: ?>
            <?php foreach($kasbon as $k): ?>
                <?php 
                $initial = !empty($k['nama_lengkap']) ? strtoupper(substr($k['nama_lengkap'], 0, 1)) : 'K';
                $stDirektur = $k['status_direktur'] ?? 'Menunggu';
                $stOverall = $k['status_keseluruhan'] ?? 'Belum Lunas';

                $pillClass = 'status-pill-menunggu';
                if ($stOverall === 'Lunas') {
                    $pillClass = 'status-pill-lunas';
                } elseif ($stDirektur === 'Disetujui') {
                    $pillClass = 'status-pill-disetujui';
                } elseif ($stDirektur === 'Ditolak') {
                    $pillClass = 'status-pill-ditolak';
                }

                $sisaPinjaman = floatval(isset($k['sisa_pinjaman']) && $k['sisa_pinjaman'] !== null ? $k['sisa_pinjaman'] : $k['jumlah_kasbon']);
                $sudahDibayar = floatval($k['jumlah_kasbon'] ?? 0) - $sisaPinjaman;
                ?>
                <div class="col-12 kasbon-card-wrapper">
                    <div class="card kasbon-card-modern p-3 p-sm-4">
                        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-glow-kasbon text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
                                    <?= $initial ?>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <h3 class="mb-0 fw-bold text-dark" style="font-size: 1.15rem;">
                                            <?= esc($k['nama_lengkap'] ?? '-') ?>
                                        </h3>
                                        <span class="id-tag"><?= esc($k['nomor_kasbon'] ?? 'KSB') ?></span>
                                    </div>
                                    <div class="mt-1 d-flex align-items-center gap-2">
                                        <span class="status-pill <?= $pillClass ?>">
                                            <i class="fas fa-info-circle me-1"></i> <?= esc($stOverall === 'Lunas' ? 'Lunas' : $stDirektur) ?>
                                        </span>
                                        <small class="text-muted text-xs"><i class="fas fa-calendar-alt me-1"></i> <?= esc($k['tanggal_pengajuan'] ?? '-') ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <button type="button" class="btn-action-pill btn-action-view" data-bs-toggle="modal" data-bs-target="#modalDetailKasbon<?= $k['id'] ?>">
                                    <i class="fas fa-eye"></i> Detail
                                </button>

                                <?php if($stDirektur === 'Menunggu'): ?>
                                    <button type="button" class="btn-action-pill btn-action-approve" data-bs-toggle="modal" data-bs-target="#modalApproveKasbon<?= $k['id'] ?>">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                    <button type="button" class="btn-action-pill btn-action-reject" data-bs-toggle="modal" data-bs-target="#modalRejectKasbon<?= $k['id'] ?>">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                <?php endif; ?>

                                <button type="button" class="btn-action-pill btn-action-edit" data-bs-toggle="modal" data-bs-target="#modalEditKasbon<?= $k['id'] ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>

                                <form action="<?= base_url('direktur/keuangan/kasbon/delete/'.$k['id']) ?>" method="POST" class="d-inline">
                                    <button type="submit" class="btn-action-pill btn-action-reject" onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan kasbon ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="py-3">
                            <div class="row g-2.5">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-briefcase text-primary"></i> Jabatan
                                        </div>
                                        <div class="data-value"><?= esc($k['jabatan'] ?? '-') ?></div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-money-bill-wave text-primary"></i> Total Kasbon
                                        </div>
                                        <div class="data-value text-dark">Rp <?= number_format($k['jumlah_kasbon'] ?? 0, 0, ',', '.') ?></div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-check-circle text-success"></i> Sudah Dibayar
                                        </div>
                                        <div class="data-value text-success">Rp <?= number_format($sudahDibayar, 0, ',', '.') ?></div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-balance-scale text-warning"></i> Sisa Pinjaman
                                        </div>
                                        <div class="data-value text-danger font-weight-bold">Rp <?= number_format($sisaPinjaman, 0, ',', '.') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Detail Kasbon -->
                <div class="modal fade" id="modalDetailKasbon<?= $k['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                            <div class="modal-header bg-dark text-white border-0 py-3 px-4">
                                <h5 class="modal-title fw-bold text-sm d-flex align-items-center">
                                    <i class="fas fa-file-invoice-dollar text-warning me-2 fs-5"></i> Detail Transaksi Kasbon - <?= esc($k['nama_lengkap']) ?>
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <?php 
                                    $alasanText = !empty($k['keperluan']) ? $k['keperluan'] : (!empty($k['alasan']) ? $k['alasan'] : (!empty($k['keterangan']) ? $k['keterangan'] : 'Tidak ada catatan keperluan.'));
                                    $tglPengajuanText = !empty($k['tanggal_pengajuan']) ? $k['tanggal_pengajuan'] : (!empty($k['created_at']) ? date('d/m/Y H:i', strtotime($k['created_at'])) : '-');
                                    $metodeText = !empty($k['metode_pembayaran']) ? $k['metode_pembayaran'] : (!empty($k['rencana_pelunasan']) ? $k['rencana_pelunasan'] : 'Potong Gaji');
                                    $angsuranCount = intval($k['jumlah_angsuran'] ?? 1);
                                    if ($angsuranCount < 1) $angsuranCount = 1;
                                    $cicilanPerBulan = round(floatval($k['jumlah_kasbon'] ?? 0) / $angsuranCount);
                                ?>
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-6">
                                        <div class="card border-0 bg-light p-3 rounded-3 h-100 border border-secondary border-opacity-10">
                                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 text-sm">
                                                <i class="fas fa-user-tag me-1"></i> INFORMASI PEMOHON & PINJAMAN
                                            </h6>
                                            <div class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10 text-sm">
                                                <span class="text-muted">Nomor Kasbon:</span>
                                                <span class="fw-bold font-monospace text-dark"><?= esc($k['nomor_kasbon'] ?? 'KSB-'.$k['id']) ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10 text-sm">
                                                <span class="text-muted">Nama Karyawan:</span>
                                                <span class="fw-bold text-dark"><?= esc($k['nama_lengkap']) ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10 text-sm">
                                                <span class="text-muted">NIK / Jabatan:</span>
                                                <span class="fw-semibold text-secondary"><?= esc($k['nik'] ?? '-') ?> (<?= esc($k['jabatan'] ?? 'Staf') ?>)</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10 text-sm">
                                                <span class="text-muted">Tanggal Pengajuan:</span>
                                                <span class="fw-semibold text-dark"><?= esc($tglPengajuanText) ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1.5 text-sm">
                                                <span class="text-muted">Metode Pengembalian:</span>
                                                <span class="fw-semibold text-dark"><?= esc($metodeText) ?> (<?= $angsuranCount ?> Bulan)</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="card border-0 bg-light p-3 rounded-3 h-100 border border-secondary border-opacity-10">
                                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 text-sm">
                                                <i class="fas fa-calculator me-1"></i> RINCIAN NOMINAL & STATUS
                                            </h6>
                                            <div class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10 text-sm">
                                                <span class="text-muted">Total Plafon Pinjaman:</span>
                                                <span class="fw-bold text-primary">Rp <?= number_format($k['jumlah_kasbon'] ?? 0, 0, ',', '.') ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10 text-sm">
                                                <span class="text-muted">Sudah Dibayar (Payroll):</span>
                                                <span class="fw-bold text-success">Rp <?= number_format($sudahDibayar, 0, ',', '.') ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10 text-sm">
                                                <span class="text-muted">Sisa Pinjaman Aktif:</span>
                                                <span class="fw-bold text-danger">Rp <?= number_format($sisaPinjaman, 0, ',', '.') ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10 text-sm">
                                                <span class="text-muted">Estimasi Cicilan / Bulan:</span>
                                                <span class="fw-bold text-dark">Rp <?= number_format($cicilanPerBulan, 0, ',', '.') ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1.5 text-sm">
                                                <span class="text-muted">Status Approval & Pelunasan:</span>
                                                <span>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-0.5 rounded-pill text-xs me-1"><?= esc($stDirektur) ?></span>
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-0.5 rounded-pill text-xs"><?= esc($stOverall) ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 rounded-3 p-3 bg-white border border-light shadow-xs mb-3">
                                    <label class="fw-bold text-dark text-xs text-uppercase text-secondary mb-1"><i class="fas fa-comment-alt me-1 text-primary"></i> Keperluan / Alasan Pengajuan Kasbon:</label>
                                    <p class="mb-0 text-dark text-sm fw-semibold"><?= nl2br(esc($alasanText)) ?></p>
                                </div>

                                <?php if (!empty($k['catatan'])): ?>
                                    <div class="card border-0 rounded-3 p-3 bg-warning bg-opacity-10 border border-warning border-opacity-20">
                                        <label class="fw-bold text-dark text-xs text-uppercase text-warning mb-1"><i class="fas fa-sticky-note me-1"></i> Catatan Direktur / Admin:</label>
                                        <p class="mb-0 text-dark text-sm"><?= nl2br(esc($k['catatan'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer bg-light border-top py-2.5 px-4 rounded-bottom-4">
                                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold text-sm" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit Kasbon -->
                <div class="modal fade" id="modalEditKasbon<?= $k['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-bottom py-3 px-4">
                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                                    <i class="fas fa-edit text-primary me-2"></i> Edit Kasbon - <?= esc($k['nama_lengkap']) ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="<?= base_url('direktur/keuangan/kasbon/simpan') ?>" method="POST">
                                <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                <input type="hidden" name="karyawan_id" value="<?= $k['karyawan_id'] ?>">
                                <div class="modal-body p-4">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-sm">Jumlah Kasbon Total (Rp)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                                <input type="text" name="jumlah_kasbon" class="form-control rounded-end input-rupiah" value="<?= number_format($k['jumlah_kasbon'] ?? 0, 0, ',', '.') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-sm">Sisa Pinjaman Aktif (Rp)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                                <input type="text" name="sisa_pinjaman" class="form-control rounded-end input-rupiah" value="<?= number_format($sisaPinjaman, 0, ',', '.') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-sm">Status Persetujuan Direktur</label>
                                            <select name="status_direktur" class="form-select rounded-3">
                                                <option value="Disetujui" <?= $stDirektur === 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                                <option value="Menunggu" <?= $stDirektur === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                                <option value="Ditolak" <?= $stDirektur === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-sm">Rencana Pelunasan</label>
                                            <input type="text" name="rencana_pelunasan" class="form-control rounded-3" value="<?= esc($k['rencana_pelunasan'] ?? '') ?>">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold text-sm">Alasan Pengajuan Kasbon</label>
                                            <textarea name="alasan" class="form-control rounded-3" rows="3"><?= esc($k['alasan'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Update Kasbon</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Approve -->
                <div class="modal fade" id="modalApproveKasbon<?= $k['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-bottom py-3 px-4">
                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i> Setujui Pengajuan Kasbon
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="<?= base_url('direktur/keuangan/kasbon/approve') ?>" method="POST">
                                <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                <div class="modal-body p-4">
                                    <p class="text-dark">Apakah Anda yakin ingin menyetujui pengajuan kasbon sebesar <strong>Rp <?= number_format($k['jumlah_kasbon'] ?? 0, 0, ',', '.') ?></strong> untuk <strong><?= esc($k['nama_lengkap']) ?></strong>?</p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-sm">Catatan Persetujuan (Opsional)</label>
                                        <input type="text" name="catatan" class="form-control rounded-3" placeholder="Catatan persetujuan...">
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">Ya, Setujui</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Reject -->
                <div class="modal fade" id="modalRejectKasbon<?= $k['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-bottom py-3 px-4">
                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                                    <i class="fas fa-times-circle text-danger me-2"></i> Tolak Pengajuan Kasbon
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="<?= base_url('direktur/keuangan/kasbon/reject') ?>" method="POST">
                                <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                <div class="modal-body p-4">
                                    <p class="text-dark">Apakah Anda yakin ingin menolak kasbon untuk <strong><?= esc($k['nama_lengkap']) ?></strong>?</p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-sm">Alasan Penolakan *</label>
                                        <textarea name="alasan" class="form-control rounded-3" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Ya, Tolak Kasbon</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchKasbon');
    const cardWrappers = document.querySelectorAll('.kasbon-card-wrapper');

    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase().trim();

            cardWrappers.forEach(card => {
                const text = card.textContent.toLowerCase();
                if(text.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Helper Rupiah Format for Input
    function formatRupiahString(numStr) {
        if (!numStr) return '';
        let number_string = numStr.toString().replace(/[^0-9]/g, '');
        if (!number_string) return '';
        let sisa = number_string.length % 3;
        let rupiah = number_string.substr(0, sisa);
        let ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('input-rupiah')) {
            let cursorPosition = e.target.selectionStart;
            let originalLength = e.target.value.length;
            
            e.target.value = formatRupiahString(e.target.value);
            
            let newLength = e.target.value.length;
            cursorPosition = cursorPosition + (newLength - originalLength);
            e.target.setSelectionRange(cursorPosition, cursorPosition);
        }
    });
});
</script>

<?= view('direktur/templates/footer', $templateData) ?>
