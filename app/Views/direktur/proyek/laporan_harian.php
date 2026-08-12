<?php
$title = $title ?? 'Laporan Kerja Harian';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'proyek'
];
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>

<style>
    /* Styling Premium Modern Material & Glassmorphism */
    .employee-card-modern {
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
    
    .employee-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
    }

    /* Avatar Soft Glowing Ring */
    .avatar-glow {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 0 4px 14px rgba(30, 60, 114, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
    }

    /* Status Pill Frosted Glass */
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

    .status-pill-revisi {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-menunggu {
        background: rgba(108, 117, 125, 0.12);
        color: #6c757d;
        border: 1px solid rgba(108, 117, 125, 0.25);
    }

    /* Bilah Data Horizontal (Data Bars) */
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
        color: #1e293b;
    }

    /* Action Buttons (Modern Pills) */
    .btn-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 600;
        transition: all 0.2s;
        border: 1px solid transparent;
        text-decoration: none;
    }
    
    .btn-action-view {
        background: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
        border-color: rgba(13, 110, 253, 0.2);
    }

    .btn-action-view:hover {
        background: #0d6efd;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
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
</style>

<div class="container-fluid py-3 py-md-4 fade-in pb-5">
    <!-- Header Title & Tambah Button (Modern Layout) -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-file-signature fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Laporan Kerja Harian</h4>
                <small class="text-muted d-none d-sm-inline">Catat dan laporkan aktivitas harian Anda</small>
            </div>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahLaporanModal">
            <i class="fas fa-plus me-1.5"></i> <span class="d-none d-md-inline">Buat Laporan Baru</span><span class="d-inline d-md-none">Lapor</span>
        </button>
    </div>

    <!-- Search & Filter Bar (Input Group Dengan Centered Modal Filter) -->
    <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchLaporan" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari laporan...">
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Laporan List Grid (Modern Cards) -->
    <div class="row g-3" id="laporanCardContainer">
        <?php if (empty($laporan)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3"><i class="fas fa-file-invoice fa-4x opacity-25"></i></div>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Laporan Harian</h6>
                <p class="text-muted small mb-3">Klik tombol di atas untuk mulai membuat laporan harian Anda.</p>
            </div>
        <?php else: ?>
            <?php foreach ($laporan as $lap): ?>
                <?php
                    $status = $lap['status'];
                    $statusPillClass = 'status-pill-menunggu';
                    $statusIcon = 'fas fa-clock';
                    $statusLabel = 'Menunggu';

                    if ($status === 'disetujui') {
                        $statusPillClass = 'status-pill-disetujui';
                        $statusIcon = 'fas fa-check-circle';
                        $statusLabel = 'Disetujui';
                    } elseif ($status === 'revisi') {
                        $statusPillClass = 'status-pill-revisi';
                        $statusIcon = 'fas fa-exclamation-triangle';
                        $statusLabel = 'Revisi';
                    }
                    
                    $initial = strtoupper(substr($lap['judul'], 0, 1));
                    $lapIdTag = 'L' . str_pad($lap['id'], 3, '0', STR_PAD_LEFT);
                ?>
                <div class="col-12 col-xl-6 laporan-card-wrapper" data-status="<?= esc($status) ?>">
                    <div class="card employee-card-modern p-3 p-sm-4 h-100">
                        
                        <!-- Visual Header Kartu -->
                        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Avatar Lingkaran Inisial -->
                                <div class="avatar-glow text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
                                    <?= $initial ?>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <!-- Judul Laporan -->
                                        <h3 class="mb-0 fw-bold text-dark" style="font-size: 1.15rem; letter-spacing: -0.2px;">
                                            <?= esc($lap['judul']) ?>
                                        </h3>
                                        <!-- Tag ID -->
                                        <span class="id-tag">ID: <?= $lapIdTag ?></span>
                                    </div>
                                    <!-- Lencana Status Chip Frosted Glass -->
                                    <div class="mt-1.5 d-flex align-items-center gap-2 flex-wrap">
                                        <span class="status-pill <?= $statusPillClass ?>">
                                            <i class="<?= $statusIcon ?> me-1"></i> <?= $statusLabel ?>
                                        </span>
                                        <span class="badge bg-light text-dark border font-weight-normal" style="border-radius: 6px; font-size: 0.72rem;">
                                            <i class="fas fa-calendar-day me-1 text-primary"></i> <?= date('d M Y', strtotime($lap['tanggal'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Penataan Bidang Data (Bilah Data Horizontal Grid) -->
                        <div class="py-3 flex-grow-1">
                            <div class="data-pill-bar">
                                <div class="data-label">
                                    <i class="fas fa-align-left text-primary"></i> Deskripsi Pekerjaan
                                </div>
                                <div class="data-value" style="font-weight: 500; font-size: 0.85rem; max-height: 3.6em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    <?= !empty($lap['deskripsi']) ? esc($lap['deskripsi']) : '<i>Tidak ada deskripsi</i>' ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Terintegrasi (Modern Action Pills) -->
                        <div class="pt-2 border-top border-light d-flex justify-content-end align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn-action-pill btn-action-view w-100 justify-content-center" data-bs-toggle="modal" data-bs-target="#detailLaporanModal<?= $lap['id'] ?>" title="Lihat Detail Laporan">
                                <i class="fas fa-eye"></i> Lihat Detail Laporan
                            </button>
                        </div>

                    </div>
                </div>
                
                <!-- Modal Detail -->
                <div class="modal fade" id="detailLaporanModal<?= $lap['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header border-bottom py-3 px-4">
                                <h5 class="modal-title fw-bold text-dark">
                                    <i class="fas fa-file-alt text-primary me-2"></i> Detail Laporan Harian
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-start">
                                <h5 class="fw-bold mb-1 text-dark"><?= esc($lap['judul']) ?></h5>
                                <div class="mb-4 d-flex gap-2">
                                    <span class="badge bg-light text-dark border"><i class="fas fa-calendar-day me-1"></i> <?= date('d M Y', strtotime($lap['tanggal'])) ?></span>
                                    <span class="badge bg-light text-dark border"><i class="<?= $statusIcon ?> me-1"></i> <?= $statusLabel ?></span>
                                </div>
                                
                                <div class="bg-light p-3 rounded-3 border border-light mb-3">
                                    <p class="mb-0" style="font-size:0.9rem; white-space:pre-wrap;"><?= esc($lap['deskripsi']) ?></p>
                                </div>
                                
                                <?php if($lap['lampiran']): ?>
                                    <div class="mb-3">
                                        <div class="fw-semibold text-muted small mb-1"><i class="fas fa-paperclip me-1"></i> Lampiran Laporan:</div>
                                        <a href="<?= base_url('uploads/laporan/'.$lap['lampiran']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-download me-1"></i> Unduh Lampiran
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if($lap['komentar_direktur']): ?>
                                    <div class="alert alert-info border-0 shadow-sm mt-3 mb-0 rounded-3">
                                        <div class="fw-bold mb-1"><i class="fas fa-comment-dots me-1"></i> Komentar/Review Direktur:</div>
                                        <div style="font-size:0.9rem; white-space:pre-wrap;"><?= esc($lap['komentar_direktur']) ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    // JS Filter logic for instant search in cards
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchLaporan");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();
                const cards = document.querySelectorAll(".laporan-card-wrapper");
                
                cards.forEach(card => {
                    const textContent = card.textContent.toLowerCase();
                    if (textContent.includes(keyword)) {
                        card.style.display = "";
                    } else {
                        card.style.display = "none";
                    }
                });
            });
        }
    });
</script>

<!-- Modal Tambah Laporan -->
<div class="modal fade" id="tambahLaporanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Laporan Kerja Harian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/proyek/laporan-harian/simpan') ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul Laporan</label>
                        <input type="text" class="form-control" name="judul" required placeholder="Cth: Instalasi Server di Klien X">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Pekerjaan</label>
                        <textarea class="form-control" name="deskripsi" rows="5" required placeholder="Ceritakan detail pekerjaan yang dilakukan hari ini..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran (Opsional)</label>
                        <input type="file" class="form-control" name="lampiran">
                        <small class="text-muted">Format: jpg, png, pdf max 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $templateData) ?>
