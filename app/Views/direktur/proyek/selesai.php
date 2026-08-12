<?php
// app/Views/direktur/proyek/selesai.php

$title = $title ?? 'Project Selesai & Arsip';
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

    .avatar-glow.bg-success {
        background: linear-gradient(135deg, #198754 0%, #157347 100%);
        box-shadow: 0 4px 14px rgba(25, 135, 84, 0.35);
    }
    
    .avatar-glow.bg-danger {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        box-shadow: 0 4px 14px rgba(220, 53, 69, 0.35);
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
    
    .status-pill-selesai {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .status-pill-batal {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
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
    <!-- Header Title & Tambah Button -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-archive fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Project Selesai & Arsip</h4>
                <small class="text-muted d-none d-sm-inline">Daftar arsip seluruh project yang telah selesai atau dibatalkan</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahArsipModal">
                <i class="fas fa-plus me-1.5"></i> <span>+ Tambah Arsip Selesai</span>
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchArsip" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari nama project, kode, atau status...">
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Project Selesai Grid -->
    <div class="row g-3" id="arsipCardContainer">
        <?php if (empty($projects)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3"><i class="fas fa-archive fa-4x opacity-25"></i></div>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Project Selesai / Arsip</h6>
                <p class="text-muted small mb-3">Project yang selesai atau dibatalkan akan tersimpan rapi di sini.</p>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahArsipModal">
                    <i class="fas fa-plus me-1"></i> Tambah Arsip Selesai
                </button>
            </div>
        <?php else: ?>
            <?php foreach ($projects as $p): ?>
                <?php
                    $status = strtolower($p['status']);
                    $statusPillClass = 'status-pill-selesai';
                    $statusIcon = 'fas fa-check-circle';
                    $statusLabel = 'Selesai';
                    $avatarColor = 'bg-success';

                    if ($status === 'batal') {
                        $statusPillClass = 'status-pill-batal';
                        $statusIcon = 'fas fa-times-circle';
                        $statusLabel = 'Dibatalkan';
                        $avatarColor = 'bg-danger';
                    }
                    
                    $initial = strtoupper(substr($p['nama_project'], 0, 1));
                ?>
                <div class="col-12 col-xl-6 arsip-card-wrapper" data-status="<?= esc($status) ?>">
                    <div class="card employee-card-modern p-3 p-sm-4 h-100">
                        
                        <!-- Visual Header Kartu -->
                        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Avatar Lingkaran Inisial -->
                                <div class="avatar-glow <?= $avatarColor ?> text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
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
                                        <span class="status-pill <?= $statusPillClass ?>">
                                            <i class="<?= $statusIcon ?> me-1"></i> <?= $statusLabel ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Penataan Bidang Data -->
                        <div class="py-3 flex-grow-1">
                            <div class="row g-2.5">
                                <div class="col-sm-4">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-coins text-success"></i> Nilai Project (Rp)
                                        </div>
                                        <div class="data-value" style="font-weight: 700; color: #198754;">
                                            Rp <?= number_format($p['nilai_project'], 0, ',', '.') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-calendar-alt text-primary"></i> Tanggal Mulai
                                        </div>
                                        <div class="data-value" style="font-weight: 500;">
                                            <?= !empty($p['tanggal_mulai']) ? date('d M Y', strtotime($p['tanggal_mulai'])) : '<i class="text-muted">Tidak Ada</i>' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-calendar-check text-danger"></i> Tanggal Selesai
                                        </div>
                                        <div class="data-value" style="font-weight: 500;">
                                            <?= !empty($p['tanggal_selesai']) ? date('d M Y', strtotime($p['tanggal_selesai'])) : '<i class="text-muted">Tidak Ada</i>' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-align-left text-info"></i> Deskripsi Singkat
                                        </div>
                                        <div class="data-value" style="font-weight: 500; font-size: 0.85rem; max-height: 3.6em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            <?= !empty($p['deskripsi']) ? esc($p['deskripsi']) : '<i>Tidak ada deskripsi</i>' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tombol Aksi CRUD Lengkap (Detail, Edit, Histori, Hapus) -->
                        <div class="pt-3 border-top border-light mt-auto d-flex align-items-center justify-content-end gap-1.5 flex-wrap">
                            <a href="<?= base_url('direktur/proyek/detail/'.$p['id']) ?>" class="btn-action-pill btn-action-view" title="Detail Project">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <a href="<?= base_url('direktur/proyek/edit/'.$p['id']) ?>" class="btn-action-pill text-warning border-warning bg-warning bg-opacity-10" title="Edit Project">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?= base_url('direktur/proyek/timeline/'.$p['id']) ?>" class="btn-action-pill text-info border-info bg-info bg-opacity-10" title="Histori Timeline">
                                <i class="fas fa-history"></i> Histori
                            </a>
                            <button type="button" onclick="confirmDeleteArsip(<?= $p['id'] ?>, '<?= esc($p['nama_project'], 'js') ?>')" class="btn-action-pill text-danger border-danger bg-danger bg-opacity-10" title="Hapus Arsip">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Arsip Project Selesai -->
<div class="modal fade" id="tambahArsipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3 px-4">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-archive me-2"></i> Tambah Arsip Project Selesai / Batal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/proyek/selesai/simpan') ?>" method="POST">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Nama Project *</label>
                            <input type="text" class="form-control rounded-3" name="nama_project" id="nama_project_input" list="list_existing_projects" placeholder="Pilih dari daftar atau ketik baru..." required autocomplete="off">
                            <datalist id="list_existing_projects">
                                <?php foreach(($existing_projects ?? []) as $ep): ?>
                                    <option value="<?= esc($ep) ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-xs text-dark mb-0">Client / Klien *</label>
                                <button type="button" class="btn btn-link p-0 text-decoration-none text-xs fw-bold text-primary" data-bs-toggle="modal" data-bs-target="#tambahClientModal">
                                    <i class="fas fa-plus-circle me-1"></i> + Tambah Client
                                </button>
                            </div>
                            <select class="form-select rounded-3" name="client_id" id="client_select" required>
                                <option value="">-- Pilih Client / Klien --</option>
                                <?php foreach(($clients ?? []) as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= esc($c['nama_perusahaan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Deskripsi Project</label>
                        <textarea class="form-control rounded-3" name="deskripsi" rows="2" placeholder="Catatan singkat penyelesaian project..."></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-xs text-dark">Nilai Project (Rp)</label>
                            <input type="text" class="form-control rounded-3" name="nilai_project" id="input_nilai_project" placeholder="Rp 0" onkeyup="formatRupiahInput(this)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-xs text-dark">Tanggal Mulai</label>
                            <input type="date" class="form-control rounded-3" name="tanggal_mulai" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-xs text-dark">Tanggal Selesai / Ditutup</label>
                            <input type="date" class="form-control rounded-3" name="tanggal_selesai" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Status Arsip *</label>
                            <select class="form-select rounded-3" name="status" required>
                                <option value="selesai">Selesai (Completed)</option>
                                <option value="batal">Batal (Cancelled)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Project Manager (PIC)</label>
                            <select class="form-select rounded-3" name="project_manager_id">
                                <option value="">-- Pilih Project Manager --</option>
                                <?php foreach(($managers ?? []) as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= esc($m['username']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2.5 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="fas fa-plus me-1.5"></i> Simpan Arsip Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Client Modal -->
<div class="modal fade" id="tambahClientModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3 px-4">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-building me-2"></i> + Tambah Client Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formQuickClient" onsubmit="submitQuickClient(event)">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Nama Perusahaan Client *</label>
                        <input type="text" class="form-control rounded-3" name="nama_perusahaan" id="qc_nama_perusahaan" required placeholder="Cth: PT. Energi Persada Nusantara">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Nama Kontak PIC</label>
                        <input type="text" class="form-control rounded-3" name="nama_kontak" id="qc_nama_kontak" placeholder="Cth: Bpk. Hendra">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Telepon / Whatsapp</label>
                        <input type="text" class="form-control rounded-3" name="telepon" id="qc_telepon" placeholder="Cth: 08123456789">
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2.5 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" id="btnSubmitClient">
                        <i class="fas fa-save me-1.5"></i> Simpan Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function formatRupiahInput(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            input.value = 'Rp ' + parseInt(value, 10).toLocaleString('id-ID');
        } else {
            input.value = '';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchArsip");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();
                const cards = document.querySelectorAll(".arsip-card-wrapper");
                
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

    function submitQuickClient(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitClient');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

        const formData = new FormData();
        formData.append('nama_perusahaan', document.getElementById('qc_nama_perusahaan').value);
        formData.append('nama_kontak', document.getElementById('qc_nama_kontak').value);
        formData.append('telepon', document.getElementById('qc_telepon').value);

        fetch('<?= base_url('direktur/proyek/simpan_client') ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1.5"></i> Simpan Client';
            if (data.status === 'success') {
                const select = document.getElementById('client_select');
                const opt = document.createElement('option');
                opt.value = data.client.id;
                opt.textContent = data.client.nama_perusahaan;
                opt.selected = true;
                select.appendChild(opt);

                const modal = bootstrap.Modal.getInstance(document.getElementById('tambahClientModal'));
                if (modal) modal.hide();
                document.getElementById('formQuickClient').reset();

                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Client baru berhasil ditambahkan.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-4 shadow-lg' }
                });
            } else {
                Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1.5"></i> Simpan Client';
            Swal.fire('Gagal!', 'Tidak dapat menghubungkan ke server.', 'error');
        });
    }

    function confirmDeleteArsip(id, nama) {
        Swal.fire({
            title: 'Hapus Arsip Project?',
            text: 'Data arsip proyek "' + nama + '" akan dihapus permanen dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('direktur/proyek/selesai/delete') ?>/' + id;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

<?= view('direktur/templates/footer', $templateData) ?>
