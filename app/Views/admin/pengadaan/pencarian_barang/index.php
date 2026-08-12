<?php
$title = $title ?? 'Penugasan Pencarian Barang & RAB';
$templateData = [
    'title' => $title,
    'user'  => $user ?? ['name' => 'Admin', 'role' => 'admin'],
    'active' => 'pengadaan'
];
?>

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

<style>
    .admin-card-modern {
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
    
    .admin-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
    }

    .avatar-glow {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 0 4px 14px rgba(30, 60, 114, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 48px;
        height: 48px;
        font-size: 1.2rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
    }
    
    .status-pill-baru {
        background: rgba(13, 110, 253, 0.12);
        color: #0d6efd;
        border: 1px solid rgba(13, 110, 253, 0.25);
    }

    .status-pill-proses {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-selesai {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .data-pill-bar {
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        padding: 10px 14px;
        height: 100%;
    }

    .data-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
    }

    .data-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
    }

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
</style>

<div class="container-fluid py-3 py-md-4 fade-in pb-5">
    <!-- Header Title -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-search-dollar fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Penugasan Pencarian Barang & RAB</h4>
                <small class="text-muted d-none d-sm-inline">Daftar instruksi dari Direktur untuk mencari harga barang & estimasi RAB</small>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Search Bar -->
    <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchBarang" class="form-control border-start-0 ps-1 py-2.5" placeholder="Cari penugasan barang / marketplace...">
    </div>

    <!-- Cards Grid -->
    <div class="row g-3" id="penugasanCardContainer">
        <?php if (empty($penugasan)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3"><i class="fas fa-box-open fa-4x opacity-25"></i></div>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Penugasan Pencarian Barang</h6>
                <p class="text-muted small">Penugasan dari Direktur untuk mencari barang & perbandingan harga RAB akan tampil di sini.</p>
            </div>
        <?php else: ?>
            <?php foreach ($penugasan as $p): ?>
                <?php
                    $st = strtolower($p['status']);
                    $stPill = 'status-pill-baru';
                    $stLabel = 'Instruksi Baru';
                    $stIcon = 'fas fa-plus-circle';

                    if ($st === 'proses') {
                        $stPill = 'status-pill-proses';
                        $stLabel = 'Sedang Dicari';
                        $stIcon = 'fas fa-spinner fa-spin';
                    } elseif ($st === 'selesai') {
                        $stPill = 'status-pill-selesai';
                        $stLabel = 'Selesai / Terkirim';
                        $stIcon = 'fas fa-check-circle';
                    }

                    $initial = strtoupper(substr($p['judul'], 0, 1));
                ?>
                <div class="col-12 col-xl-6 penugasan-card-wrapper">
                    <div class="card admin-card-modern p-3 p-sm-4 h-100">
                        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-glow text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
                                    <?= $initial ?>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.1rem;"><?= esc($p['judul']) ?></h5>
                                    <div class="mt-1.5 d-flex align-items-center gap-2 flex-wrap">
                                        <span class="status-pill <?= $stPill ?>">
                                            <i class="<?= $stIcon ?> me-1"></i> <?= $stLabel ?>
                                        </span>
                                        <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.72rem;">
                                            <i class="fas fa-user me-1 text-primary"></i> Pembuat: <?= esc($p['pembuat_tugas'] ?? 'Direktur') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="py-3 flex-grow-1">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="data-pill-bar">
                                        <div class="data-label"><i class="fas fa-store me-1 text-primary"></i> Marketplace / Toko</div>
                                        <div class="data-value"><?= !empty($p['nama_toko_marketplace']) ? esc($p['nama_toko_marketplace']) : '<i class="text-muted fw-normal">Belum diisi</i>' ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="data-pill-bar">
                                        <div class="data-label"><i class="fas fa-coins me-1 text-success"></i> Estimasi RAB (Rp)</div>
                                        <div class="data-value text-success font-weight-bold">
                                            <?= !empty($p['nominal_estimasi']) ? 'Rp ' . number_format($p['nominal_estimasi'], 0, ',', '.') : '<i class="text-muted fw-normal">Belum diisi</i>' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="data-pill-bar">
                                        <div class="data-label"><i class="fas fa-align-left me-1 text-primary"></i> Instruksi & Deskripsi</div>
                                        <div class="data-value" style="font-size: 0.85rem; font-weight: 500; max-height: 3.2em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            <?= esc($p['deskripsi']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-top border-light d-flex justify-content-end align-items-center gap-2">
                            <a href="<?= base_url('admin/pengadaan/pencarian-barang/detail/' . $p['id']) ?>" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-semibold text-xs">
                                <i class="fas fa-edit me-1"></i> Detail & Input Hasil Pencarian
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchBarang");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const kw = this.value.toLowerCase();
                document.querySelectorAll(".penugasan-card-wrapper").forEach(card => {
                    card.style.display = card.textContent.toLowerCase().includes(kw) ? "" : "none";
                });
            });
        }
    });
</script>

<?= view('admin/templates/footer', $templateData) ?>
