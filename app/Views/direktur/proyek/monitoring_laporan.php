<?php
$title = $title ?? 'Monitoring Laporan Karyawan';
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

    .active-filter-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(30, 60, 114, 0.1);
        color: #1e3c72;
        font-weight: 600;
    }
</style>

<div class="container-fluid py-3 py-md-4 fade-in pb-5">
    <!-- Header Title & Tambah Button (Modern Layout) -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-search-dollar fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark" style="font-size: clamp(1rem, 4vw, 1.25rem);">Monitoring Laporan Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Pantau dan review laporan kerja harian seluruh staf</small>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar (Input Group Dengan Centered Modal Filter) -->
    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchMonitoring" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari nama karyawan, jabatan, atau judul laporan...">
        <button class="btn btn-dark px-4 d-flex align-items-center gap-2 fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="fas fa-sliders-h"></i> <span class="d-none d-sm-inline">Filter</span>
        </button>
    </div>

    <!-- Indicator Active Filter (Jika sedang memfilter) -->
    <?php $hasFilter = (!empty($filter_status) || !empty($filter_tanggal)); ?>
    <div id="activeFilterTags" class="d-flex align-items-center gap-2 mb-3 flex-wrap <?= $hasFilter ? '' : 'd-none' ?>">
        <span class="text-xs text-muted fw-bold">Filter Aktif:</span>
        <div id="filterTagContainer" class="d-flex gap-1 flex-wrap">
            <?php if(!empty($filter_tanggal)): ?>
                <span class="active-filter-badge">Tanggal: <?= $filter_tanggal ?></span>
            <?php endif; ?>
            <?php if(!empty($filter_status)): ?>
                <span class="active-filter-badge">Status: <?= ucfirst($filter_status) ?></span>
            <?php endif; ?>
        </div>
        <a href="<?= base_url('direktur/proyek/monitoring-laporan') ?>" class="btn btn-link btn-sm text-danger p-0 text-decoration-none ms-2" style="font-size: 0.8rem;">
            <i class="fas fa-times-circle me-1"></i> Hapus Filter
        </a>
    </div>

    <!-- Modal Filter Lanjutan (Centered & Responsive) -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="filterModalLabel">
                        <i class="fas fa-filter text-primary me-2"></i> Filter Monitoring
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="<?= base_url('direktur/proyek/monitoring-laporan') ?>">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm">Tanggal Laporan</label>
                                <input type="date" name="tanggal" class="form-control rounded-3" value="<?= htmlspecialchars($filter_tanggal) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm">Status Review</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="">Semua Status</option>
                                    <option value="menunggu_review" <?= $filter_status == 'menunggu_review' ? 'selected' : '' ?>>Menunggu Review</option>
                                    <option value="disetujui" <?= $filter_status == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                    <option value="revisi" <?= $filter_status == 'revisi' ? 'selected' : '' ?>>Revisi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                        <a href="<?= base_url('direktur/proyek/monitoring-laporan') ?>" class="btn btn-light rounded-pill px-4 fw-semibold border">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="fas fa-check me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Monitoring List Grid (Modern Cards) -->
    <div class="row g-3" id="monitoringCardContainer">
        <?php if (empty($laporan)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3"><i class="fas fa-search-minus fa-4x opacity-25"></i></div>
                <h6 class="fw-bold text-dark mb-1">Tidak Ada Laporan</h6>
                <p class="text-muted small mb-3">Tidak ada laporan dari staf pada tanggal/filter ini.</p>
            </div>
        <?php else: ?>
            <?php foreach ($laporan as $lap): ?>
                <?php
                    $status = $lap['status'];
                    $statusPillClass = 'status-pill-menunggu';
                    $statusIcon = 'fas fa-clock';
                    $statusLabel = 'Menunggu Review';

                    if ($status === 'disetujui') {
                        $statusPillClass = 'status-pill-disetujui';
                        $statusIcon = 'fas fa-check-circle';
                        $statusLabel = 'Disetujui';
                    } elseif ($status === 'revisi') {
                        $statusPillClass = 'status-pill-revisi';
                        $statusIcon = 'fas fa-exclamation-triangle';
                        $statusLabel = 'Revisi';
                    }
                    
                    $initial = strtoupper(substr($lap['nama_lengkap'], 0, 1));
                    $lapIdTag = 'M' . str_pad($lap['id'], 3, '0', STR_PAD_LEFT);
                ?>
                <div class="col-12 col-xl-6 monitoring-card-wrapper" data-status="<?= esc($status) ?>">
                    <div class="card employee-card-modern p-3 p-sm-4 h-100">
                        
                        <!-- Visual Header Kartu -->
                        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Avatar Lingkaran Inisial -->
                                <div class="avatar-glow text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
                                    <?= $initial ?>
                                </div>
                                <div style="min-width:0;">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <!-- Nama Karyawan -->
                                        <h3 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.15rem; letter-spacing: -0.2px;">
                                            <?= esc($lap['nama_lengkap']) ?>
                                        </h3>
                                        <!-- Tag ID -->
                                        <span class="id-tag">ID: <?= $lapIdTag ?></span>
                                    </div>
                                    <!-- Jabatan & Status -->
                                    <div class="mt-1.5 d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-light text-dark border font-weight-normal" style="border-radius: 6px; font-size: 0.72rem;">
                                            <i class="fas fa-briefcase me-1 text-primary"></i> <?= esc($lap['jabatan']) ?>
                                        </span>
                                        <span class="status-pill <?= $statusPillClass ?>">
                                            <i class="<?= $statusIcon ?> me-1"></i> <?= $statusLabel ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Penataan Bidang Data (Bilah Data Horizontal Grid) -->
                        <div class="py-3 flex-grow-1">
                            <div class="row g-2.5">
                                <div class="col-12">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-align-left text-primary"></i> Laporan: <?= esc($lap['judul']) ?> (<?= date('d M Y', strtotime($lap['tanggal'])) ?>)
                                        </div>
                                        <div class="data-value" style="font-weight: 500; font-size: 0.85rem; max-height: 3.6em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            <?= !empty($lap['deskripsi']) ? esc($lap['deskripsi']) : '<i>Tidak ada deskripsi</i>' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Terintegrasi (Modern Action Pills) -->
                        <div class="pt-2 border-top border-light d-flex justify-content-end align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn-action-pill btn-action-view w-100 justify-content-center" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $lap['id'] ?>" title="Review Laporan">
                                <i class="fas fa-search"></i> Review Laporan
                            </button>
                        </div>

                    </div>
                </div>
                            
                            <!-- Modal Review -->
                            <div class="modal fade" id="reviewModal<?= $lap['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Review Laporan Harian</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="<?= base_url('direktur/proyek/monitoring-laporan/approve') ?>" method="POST">
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="id" value="<?= $lap['id'] ?>">
                                                
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">Karyawan</small>
                                                        <div class="fw-bold"><?= esc($lap['nama_lengkap']) ?></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">Tanggal Laporan</small>
                                                        <div class="fw-bold"><?= date('d M Y', strtotime($lap['tanggal'])) ?></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <small class="text-muted d-block">Judul Laporan</small>
                                                    <div class="fw-bold fs-5"><?= esc($lap['judul']) ?></div>
                                                </div>
                                                
                                                <div class="mb-4 p-3 bg-light rounded">
                                                    <small class="text-muted d-block mb-2">Deskripsi Kerja</small>
                                                    <p class="mb-0" style="white-space: pre-wrap;"><?= esc($lap['deskripsi']) ?></p>
                                                </div>
                                                
                                                <?php if($lap['lampiran']): ?>
                                                <div class="mb-4">
                                                    <a href="<?= base_url('uploads/laporan/'.$lap['lampiran']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-paperclip me-2"></i>Lihat Lampiran
                                                    </a>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <hr>
                                                
                                                <h6 class="fw-bold mb-3">Tindakan Review</h6>
                                                <div class="mb-3">
                                                    <label class="form-label">Ubah Status</label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="menunggu_review" <?= $lap['status'] == 'menunggu_review' ? 'selected' : '' ?>>Menunggu Review</option>
                                                        <option value="disetujui" <?= $lap['status'] == 'disetujui' ? 'selected' : '' ?>>Setujui Laporan</option>
                                                        <option value="revisi" <?= $lap['status'] == 'revisi' ? 'selected' : '' ?>>Minta Revisi</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Komentar / Arahan (Opsional)</label>
                                                    <textarea class="form-control" name="komentar" rows="3"><?= esc($lap['komentar_direktur']) ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan Review</button>
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
    // JS Filter logic for instant search in cards
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchMonitoring");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();
                const cards = document.querySelectorAll(".monitoring-card-wrapper");
                
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

<?= view('direktur/templates/footer', $templateData) ?>
