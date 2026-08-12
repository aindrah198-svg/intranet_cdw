<?php
// app/Views/direktur/proyek/timeline.php

$title = $title ?? 'Timeline Kerja';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'proyek'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);
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
    
    .status-pill-proses {
        background: rgba(13, 110, 253, 0.12);
        color: #0d6efd;
        border: 1px solid rgba(13, 110, 253, 0.25);
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
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
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
    <!-- Header Title & Selection Button (Melanjutkan Proyek yang Ada) -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-stream fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Timeline Kerja (Project Aktif)</h4>
                <small class="text-muted d-none d-sm-inline">Melanjutkan eksekusi timeline harian/mingguan dari proyek yang diinisiasi di Project Baru</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAktifkanProyek">
                <i class="fas fa-play-circle me-1.5"></i> <span>Mulai Timeline Proyek</span>
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchTimeline" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari nama project, kode, atau client...">
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3" id="timelineCardContainer">
        <?php if(empty($projects)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3"><i class="fas fa-folder-open fa-4x opacity-25"></i></div>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Project Yang Sedang Berjalan (On Progress)</h6>
                <p class="text-muted small mb-3">Pilih proyek dari menu 'Project Baru' untuk memulai eksekusi timelinenya.</p>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAktifkanProyek">
                    <i class="fas fa-play-circle me-1"></i> Pilih Proyek Untuk Mulai Timeline
                </button>
            </div>
        <?php else: ?>
            <?php foreach($projects as $p): ?>
                <?php
                    $initial = strtoupper(substr($p['nama_project'], 0, 1));
                    $tglMulai = !empty($p['tanggal_mulai']) ? strtotime($p['tanggal_mulai']) : time();
                    $tglSelesai = !empty($p['tanggal_selesai']) ? strtotime($p['tanggal_selesai']) : time();
                    $durasiHari = max(1, ceil(($tglSelesai - $tglMulai) / 86400));
                ?>
                <div class="col-12 col-xl-6 timeline-card-wrapper">
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
                                        <!-- Nama Project -->
                                        <h3 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.15rem; letter-spacing: -0.2px;">
                                            <?= esc($p['nama_project']) ?>
                                        </h3>
                                        <!-- Kode Project -->
                                        <span class="id-tag">Kode: <?= esc($p['kode_project']) ?></span>
                                    </div>
                                    <!-- Lencana Status Chip Frosted Glass -->
                                    <div class="mt-1.5 d-flex align-items-center gap-2 flex-wrap">
                                        <?php if (strtolower($p['status']) === 'selesai'): ?>
                                            <span class="status-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                <i class="fas fa-check-circle me-1"></i> Selesai
                                            </span>
                                        <?php else: ?>
                                            <span class="status-pill status-pill-proses">
                                                <i class="fas fa-spinner fa-spin me-1"></i> On Progress
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Penataan Bidang Data -->
                        <div class="py-3 flex-grow-1">
                            <div class="row g-2.5">
                                <div class="col-sm-6">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-play text-primary"></i> Mulai Execution
                                        </div>
                                        <div class="data-value" style="font-weight: 600;">
                                            <?= !empty($p['tanggal_mulai']) ? date('d M Y', strtotime($p['tanggal_mulai'])) : '-' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-flag-checkered text-warning"></i> Estimasi Selesai
                                        </div>
                                        <div class="data-value" style="font-weight: 600;">
                                            <?= !empty($p['tanggal_selesai']) ? date('d M Y', strtotime($p['tanggal_selesai'])) : '<i class="text-muted">Belum Ditentukan</i>' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-align-left text-info"></i> Deskripsi Proyek
                                        </div>
                                        <div class="data-value" style="font-weight: 500; font-size: 0.85rem; max-height: 3.6em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            <?= esc($p['deskripsi'] ?: 'Tidak ada deskripsi') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Action Buttons (Detail, Export Excel, Cetak PDF, Tandai Selesai, Hapus) -->
                        <div class="pt-3 mt-auto border-top border-light d-flex align-items-center justify-content-end gap-1.5 flex-wrap">
                            <a href="<?= base_url('direktur/proyek/timeline/'.$p['id']) ?>" class="btn-action-pill btn-action-view" title="Detail Timeline & Schedule">
                                <i class="fas fa-eye"></i> Detail & Task
                            </a>
                            <a href="<?= base_url('direktur/proyek/timeline/export-excel/'.$p['id']) ?>" class="btn-action-pill text-success border-success bg-success bg-opacity-10" title="Export Excel Timeline">
                                <i class="fas fa-file-excel"></i> Excel
                            </a>
                            <a href="<?= base_url('direktur/proyek/timeline/print-pdf/'.$p['id']) ?>" target="_blank" class="btn-action-pill text-danger border-danger bg-danger bg-opacity-10" title="Cetak PDF / Print Timeline">
                                <i class="fas fa-print"></i> Cetak PDF
                            </a>
                            <?php if (strtolower($p['status']) !== 'selesai'): ?>
                                <button type="button" onclick="confirmSelesaikanProyek(<?= $p['id'] ?>, '<?= esc($p['nama_project'], 'js') ?>')" class="btn-action-pill text-success border-success bg-success bg-opacity-10" title="Tandai Proyek Selesai">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </button>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 text-xs fw-bold">
                                    <i class="fas fa-check-double me-1"></i> Telah Selesai
                                </span>
                            <?php endif; ?>
                            <button type="button" onclick="confirmDeleteTimeline(<?= $p['id'] ?>, '<?= esc($p['nama_project'], 'js') ?>')" class="btn-action-pill text-secondary border-secondary bg-secondary bg-opacity-10" title="Keluarkan dari Timeline">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Aktifkan Project ke Timeline -->
<div class="modal fade" id="modalAktifkanProyek" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3 px-4">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-play-circle me-2"></i> Mulai Timeline Proyek Dari Inisiasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/proyek/timeline/aktifkan') ?>" method="POST">
                <div class="modal-body p-4">
                    <p class="text-muted text-xs mb-3">Pilih proyek yang diinisiasi pada menu <strong>Project Baru</strong> untuk mulai disusun jadwal pelaksanaan timelinenya secara harian/mingguan.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Pilih Proyek *</label>
                        <select class="form-select rounded-3" name="proyek_id" required>
                            <option value="">-- Pilih Proyek Terdaftar --</option>
                            <?php foreach(($projects_pending ?? []) as $pp): ?>
                                <option value="<?= $pp['id'] ?>"><?= esc($pp['kode_project']) ?> - <?= esc($pp['nama_project']) ?> (Status: <?= ucfirst($pp['status']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2.5 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="fas fa-play me-1.5"></i> Mulai Timeline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchTimeline");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();
                const cards = document.querySelectorAll(".timeline-card-wrapper");
                
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

    function confirmSelesaikanProyek(id, nama) {
        Swal.fire({
            title: 'Selesaikan Proyek?',
            text: 'Proyek "' + nama + '" akan ditandai Selesai dan dipindahkan ke Arsip Project Selesai.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check me-1"></i> Ya, Selesaikan!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('direktur/proyek/timeline/selesaikan') ?>/' + id;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmDeleteTimeline(id, nama) {
        Swal.fire({
            title: 'Keluarkan dari Timeline?',
            text: 'Jadwal timeline "' + nama + '" akan dikosongkan. Data proyek di menu Project Baru tetap aman dan utuh.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-undo me-1"></i> Ya, Keluarkan!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('direktur/proyek/timeline/delete') ?>/' + id;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

<?= view('direktur/templates/footer', $templateData) ?>
